<?php

namespace App\Repositories;

class PU_CIS
{
    /**
	  * Dvije se stvari salju na fiskalizaciju: Racun(RacunZahtjev) i Poslovni prostori(PoslovniProstorZahtjev)
	 * $XMLRequestType = 'RacunZahtjev';
	 * var $XMLRequestType = 'PoslovniProstorZahtjev';
	*/

	private $XMLRequestDOMDoc;
	private $privateKeyResource;
	private $publicCertificateData;
	private $publicCertificate;
	private $certificateCApem;
	public $Jir;
	public $finaResponse;

    public function __construct($fiscal_data)
    {
		$this->XMLRequestDOMDoc = new \DOMDocument();

        /**
         * dohvacanje certifikata tvrtke (ako slucajno ima exstenziju pfx neka se samo preimenuje u p12 (negdje sam procitao da
         * linux ne voli pfx, dok je za windows ok obje exstenzije))
        */
        
        $certificate = null;
        $certificatePass = $fiscal_data['password'];        
        
        $p12Certificate = storage_path().'/app/public/certificates/'.$fiscal_data['certificate'];

        openssl_pkcs12_read(file_get_contents($p12Certificate), $certificate, $certificatePass);

        $this->publicCertificate = $certificate['cert'];
        $privateKey = $certificate['pkey'];

        $this->privateKeyResource = openssl_pkey_get_private($privateKey, $certificatePass);
        $this->publicCertificateData = openssl_x509_parse($this->publicCertificate);

        /**
         * ROOT certifikat za sve ostale DEMO FISKAL certifikate. Ovo "ROOT", pojednostavljeno,
         * znači da je taj certifikat "iznad" (certification path) svakog drugog FISKAL DEMO certifikata.
         * Kako je FINA taj certifikat sama sebi izdala (certifikat je "samopotpisan"),
         * tako znači da iznad njega nema nekog drugog ROOT certifikata.
         *
         * za Produkciju taj certifikat je RDCca.cer
        */
        
        $certificateCAcer = storage_path().'/app/public/certificates/FinaRDCCA2015.cer';
        $certificateCAcerContent = file_get_contents($certificateCAcer);		
		
        /* Convert .cer to .pem, cURL uses .pem */
        $certificateCApemContent =  '-----BEGIN CERTIFICATE-----'.PHP_EOL
            .chunk_split(base64_encode($certificateCAcerContent), 64, PHP_EOL)
            .'-----END CERTIFICATE-----'.PHP_EOL;
		
        $this->certificateCApem = $certificateCAcer.'.pem';
        file_put_contents($this->certificateCApem, $certificateCApemContent);
    }

    /**
     * ZKI
     * Zaštitni kod izdavatelja obveznika fiskalizacije je alfanumerički zapis kojim se potvrđuje veza između
     * obveznika fiskalizacije i izdanog računa. Zaštitni kod formira obveznik fiskalizacije, ispisuje ga na računu
     * i dostavlja Poreznoj upravi kao obavezni element računa.
     *
     * U nastavku je opisan pseudokod algoritma za izračun zaštitnog koda izdavatelja:
     *
     * početak
     * pročitaj (oib)
     * medjurezultat = oib
     * pročitaj (datVrij – datum i vrijeme izdavanja računa zapisani kao tekst u formatu 'dd.MM.gggg HH:mm:ss')
     * medjurezultat = medjurezultat + datVrij
     * pročitaj (bor – brojčana oznaka računa)
     * medjurezultat = medjurezultat + bor
     * pročitaj (opp – oznaka poslovnog prostora)
     * medjurezultat = medjurezultat + opp
     * pročitaj (onu – oznaka naplatnog uređaja)
     * medjurezultat = medjurezultat + onu
     * pročitaj (uir - ukupni iznos računa)
     * medjurezultat = medjurezultat + uir
     * elektronički potpiši medjurezultat koristeći RSA-SHA1 potpis
     * rezultatIspis = izračunajMD5(elektronički potpisani medjurezultat)
     * kraj
    */

    private function ZKI($dt, $fiscal_data, $is_reversed)
    {
        $ZastKodUnsigned = '';
        $ZastKodUnsigned .= $fiscal_data['oib']; //oib
        $ZastKodUnsigned .= $dt; //vrijeme izdavanja računa
        $ZastKodUnsigned .= $fiscal_data['invoice_number']; //brojčana oznaka računa
        $ZastKodUnsigned .= $fiscal_data['office_number']; //oznaka poslovnog prostora
        $ZastKodUnsigned .= $fiscal_data['register_number']; //oznaka naplatnog uređaja
        
        //check if invoice is reversed
        if ($is_reversed)
        {
			$ZastKodUnsigned .= '-'.$fiscal_data['invoice_grand_total']; //ukupan iznos računa
		}
		else
		{
        	$ZastKodUnsigned .= $fiscal_data['invoice_grand_total']; //ukupan iznos računa
        }

        $ZastKodSignature = null;

        openssl_sign($ZastKodUnsigned, $ZastKodSignature, $this->privateKeyResource, OPENSSL_ALGO_SHA1);

        $ZastKod = md5($ZastKodSignature); /* Koristeći MD5 kriptografsku hash funkciju (po standardu RFC 1321 The MD5
            Message-Digest Algorithm) */

        return $ZastKod;
    }

    /**
     * Namjena UUID-a je omogućiti distribuiranim sustavima jedinstven način
     * identifikacije bez značajnije centralizirane koordinacije. Svatko može generirati UUID i koristiti ga s
     * razumnnom dozom sigurnosti da nitko nenamjerno neće kreirati isti identifikator. Shodno tome podaci
     * označeni UUID-om mogu bez bojazni biti insertirani u jedinstvenu bazu podataka bez bojazni da će
     * nastati ikakvi ID konflikti.
     * @return string
    */
    
    private function UUIDv4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * pravljenje XMLa za Racun, koji ce se slati u poreznu
    */
    
    public function CreateRacunRequest($fiscal_data, $is_reversed)
    {        
        /* provjera naknadne dostave računa */
        if ($fiscal_data['delivery'] == 'T')
        {
			$dt = date('d.m.Y\TH:i:s', strtotime($fiscal_data['invoice_date']));
		}
		else
		{
			$dt = date('d.m.Y\TH:i:s');
		}       
        
        $ns = 'tns';

        $writer = new \XMLWriter();
        $writer->openMemory();

        $writer->setIndent(4);
        $writer->startElementNs($ns, 'RacunZahtjev', 'http://www.apis-it.hr/fin/2012/types/f73');
        $writer->writeAttribute('Id', 'signXmlId');

        $writer->startElementNs($ns, 'Zaglavlje', null);
        $writer->writeElementNs($ns, 'IdPoruke', null, $this->UUIDv4());
        $writer->writeElementNs($ns, 'DatumVrijeme', null, $dt);
        $writer->endElement(); /* #Zaglavlje */

        $writer->startElementNs($ns, 'Racun', null);
        $writer->writeElementNs($ns, 'Oib', null, $fiscal_data['oib']);
        $writer->writeElementNs($ns, 'USustPdv', null, $fiscal_data['pdv_user']); /* U sustavu pdva true/false - > '1'/'0' */
        $writer->writeElementNs($ns, 'DatVrijeme', null, $dt); // $rac->dt - ovo je datum na racunu
        $writer->writeElementNs($ns, 'OznSlijed', null, $fiscal_data['sljednost_prostor']);
        /* P ili N => P na nivou Poslovnog prostora, N na nivou naplatnog uredaja */

        $writer->startElementNs($ns, 'BrRac', null);
        $writer->writeElementNs($ns, 'BrOznRac', null, $fiscal_data['invoice_number']);
        $writer->writeElementNs($ns, 'OznPosPr', null, $fiscal_data['office_number']);
        $writer->writeElementNs($ns, 'OznNapUr', null, $fiscal_data['register_number']);
        $writer->endElement(); /* #BrRac */

        /* ako je korosnik u sustavu PDV-a, dodaj porez po stopama */
        if ($fiscal_data['pdv_user'] == 1)
        {
            /* PDV treba grupirati po stopama */
            $writer->startElementNs($ns, 'Pdv', null);

            /* dodaj porezne stope */
            foreach ($fiscal_data['taxes'] as $tax)
            {
                $writer->startElementNs($ns, 'Porez', null);
                $writer->writeElementNs($ns, 'Stopa', null, $tax['tax']);  // '25.00' -- ovo je povukao

                //check if invoice is reversed
                if ($is_reversed)
                {
                    $writer->writeElementNs($ns, 'Osnovica', null, '-'.$tax['sum']); // bilo  $rac->base '100.00' -- ovo nije
                    $writer->writeElementNs($ns, 'Iznos', null, '-'.$tax['tax_sum']); // bilo $rac->amount '125.00' -- ovo nije
                }
                else
                {
                    $writer->writeElementNs($ns, 'Osnovica', null, $tax['sum']); // bilo  $rac->base '100.00' -- ovo nije
                    $writer->writeElementNs($ns, 'Iznos', null, $tax['tax_sum']); // bilo $rac->amount '125.00' -- ovo nije
                }

                $writer->endElement(); /* #Porez */
            }

            $writer->endElement(); /* #Pdv */
        }

        //check if invoice is reversed
		if ($is_reversed)
	    {        
        	$writer->writeElementNs($ns, 'IznosUkupno', null, '-'.$fiscal_data['invoice_grand_total']);
        }
        else
        {
			$writer->writeElementNs($ns, 'IznosUkupno', null, $fiscal_data['invoice_grand_total']);
		}

        $writer->writeElementNs($ns, 'NacinPlac', null, 'G');
        /* 'G' - gotovina, 'K' - kartice, 'T' - transakcijski racun, 'C' - cekovi, 'O' - ostalo */
        $writer->writeElementNs($ns, 'OibOper', null, $fiscal_data['oib']);

        /* provjera naknadne dostave računa */
        if ($fiscal_data['delivery'] == 'T')
        {        
	        $writer->writeElementNs($ns, 'ZastKod', null, $fiscal_data['zki']);
	        $writer->writeElementNs($ns, 'NakDost', null, '1');
	        /* Naknadna dostava true/false - > '1'/'0'...u slučaju da nema interneta ili struje ovo treba biti true odnosno racun
	        je napravljen kada nije bilo interneta/struje ali se naknadno dostavlja poreznoj */
		}
		else
		{
			$writer->writeElementNs($ns, 'ZastKod', null, $this->ZKI($dt, $fiscal_data, $is_reversed));
	        $writer->writeElementNs($ns, 'NakDost', null, '0');
	        /* Naknadna dostava true/false - > '1'/'0'...u slučaju da nema interneta ili struje ovo treba biti true odnosno racun
	        je napravljen kada nije bilo interneta/struje ali se naknadno dostavlja poreznoj */
		}

        $writer->endElement(); /* #Racun */

        $writer->endElement(); /* #RacunZahtjev */

        $XMLRequest = $writer->outputMemory();

        return array('xml' => $XMLRequest, 'zki' => $this->ZKI($dt, $fiscal_data, $is_reversed));
    }
    
    public function PotpisivanjeXMLRequesta($XMLRequest)
    {
    	$this->XMLRequestDOMDoc->loadXML($XMLRequest);

        $canonical =  $this->XMLRequestDOMDoc->C14N();
        $DigestValue = base64_encode(hash('sha1', $canonical, true));

        $rootElem =  $this->XMLRequestDOMDoc->documentElement;

        $SignatureNode = $rootElem->appendChild(new \DOMElement('Signature'));
        $SignatureNode->setAttribute('xmlns','http://www.w3.org/2000/09/xmldsig#');

        $SignedInfoNode = $SignatureNode->appendChild(new \DOMElement('SignedInfo'));
        $SignedInfoNode->setAttribute('xmlns','http://www.w3.org/2000/09/xmldsig#');

        $CanonicalizationMethodNode = $SignedInfoNode->appendChild(new \DOMElement('CanonicalizationMethod'));
        $CanonicalizationMethodNode->setAttribute('Algorithm','http://www.w3.org/2001/10/xml-exc-c14n#');

        $SignatureMethodNode = $SignedInfoNode->appendChild(new \DOMElement('SignatureMethod'));
        $SignatureMethodNode->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#rsa-sha1');

        $ReferenceNode = $SignedInfoNode->appendChild(new \DOMElement('Reference'));
        $ReferenceNode->setAttribute('URI', sprintf('#%s', 'signXmlId'));

        $TransformsNode = $ReferenceNode->appendChild(new \DOMElement('Transforms'));

        $Transform1Node = $TransformsNode->appendChild(new \DOMElement('Transform'));
        $Transform1Node->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#enveloped-signature');

        $Transform2Node = $TransformsNode->appendChild(new \DOMElement('Transform'));
        $Transform2Node->setAttribute('Algorithm', 'http://www.w3.org/2001/10/xml-exc-c14n#');

        $DigestMethodNode = $ReferenceNode->appendChild(new \DOMElement('DigestMethod'));
        $DigestMethodNode->setAttribute('Algorithm','http://www.w3.org/2000/09/xmldsig#sha1');

        $ReferenceNode->appendChild(new \DOMElement('DigestValue', $DigestValue));

        $SignedInfoNode =  $this->XMLRequestDOMDoc->getElementsByTagName('SignedInfo')->item(0);

        $X509Issuer = $this->publicCertificateData['issuer'];

        if (array_key_exists('OU', $X509Issuer))
        {
            $X509IssuerName = sprintf('OU=%s,O=%s,C=%s', $X509Issuer['OU'], $X509Issuer['O'], $X509Issuer['C']);
        }
        else
        {
            $X509IssuerName = sprintf('CN=%s,O=%s,C=%s', $X509Issuer['CN'], $X509Issuer['O'], $X509Issuer['C']);
        }

        $X509IssuerSerial = $this->publicCertificateData['serialNumber'];

        $publicCertificatePureString = str_replace('-----BEGIN CERTIFICATE-----', '', $this->publicCertificate);
        $publicCertificatePureString = str_replace('-----END CERTIFICATE-----', '', $publicCertificatePureString);

        $SignedInfoSignature = null;

        if (!openssl_sign($SignedInfoNode->C14N(true), $SignedInfoSignature, $this->privateKeyResource, OPENSSL_ALGO_SHA1))
        {
            print_r('Unable to sign the request');
        }

        $SignatureNode =  $this->XMLRequestDOMDoc->getElementsByTagName('Signature')->item(0);
        $SignatureValueNode = new \DOMElement('SignatureValue', base64_encode($SignedInfoSignature));
        $SignatureNode->appendChild($SignatureValueNode);

        $KeyInfoNode = $SignatureNode->appendChild(new \DOMElement('KeyInfo'));

        $X509DataNode = $KeyInfoNode->appendChild(new \DOMElement('X509Data'));

        $X509IssuerSerialNode = $X509DataNode->appendChild(new \DOMElement('X509IssuerSerial'));

        $X509IssuerNameNode = new \DOMElement('X509IssuerName',$X509IssuerName);
        $X509IssuerSerialNode->appendChild($X509IssuerNameNode);

        $X509SerialNumberNode = new \DOMElement('X509SerialNumber',$X509IssuerSerial);
        $X509IssuerSerialNode->appendChild($X509SerialNumberNode);

        $X509CertificateNode = new \DOMElement('X509Certificate', $publicCertificatePureString);
        $X509DataNode->appendChild($X509CertificateNode); 
    }
    
    public function AddSoapMessage($XMLRequestType)
    {
        $envelope = new \DOMDocument();

        $envelope->loadXML('<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
         <soapenv:Body></soapenv:Body>
         </soapenv:Envelope>');

        $envelope->encoding = 'UTF-8';
        $envelope->version = '1.0';

        $XMLRequestTypeNode = $this->XMLRequestDOMDoc->getElementsByTagName($XMLRequestType)->item(0);
        $XMLRequestTypeNode = $envelope->importNode($XMLRequestTypeNode, true);

        $envelope->getElementsByTagName('Body')->item(0)->appendChild($XMLRequestTypeNode);

        /* signed XML request */
        $payload = $envelope->saveXML();

        return $payload;
    }

    public function SendRequest($payload)
    {
        //https://cistest.apis-it.hr:8449/FiskalizacijaServiceTest
        //https://cis.porezna-uprava.hr:8449/FiskalizacijaService

        $ch = curl_init();
		
        $options = array(
            CURLOPT_URL => 'https://cis.porezna-uprava.hr:8449/FiskalizacijaService',
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => false,
            //CURLOPT_CAINFO => $this->certificateCApem,
        );

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);

        if ($response)
        {
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
            $DOMResponse = new \DOMDocument();
            $DOMResponse->loadXML($response);

            if ($code === 200)
            {
             	$this->finaResponse = 200;
                
                /* RacunZahtjev */
                $this->Jir = $DOMResponse->getElementsByTagName('Jir')->item(0);
                
                if ($this->Jir)
                {
                    $this->Jir = $this->Jir->nodeValue;
                }
            }
            else
            {
				$this->finaResponse = 500;
				$this->Jir = '';
                
                $SifraGreske = $DOMResponse->getElementsByTagName('SifraGreske')->item(0);
                $PorukaGreske = $DOMResponse->getElementsByTagName('PorukaGreske')->item(0);
                /*
                if ($SifraGreske && $PorukaGreske)
                {
                    return $SifraGreske->nodeValue.': '.$PorukaGreske->nodeValue;
                }
                else
                {
                    return 'HTTP response code %s not suited for further actions: '.$code;
                }*/
            }

            curl_close($ch);
            
            return array('status' => $this->finaResponse, 'jir' => $this->Jir);
        }
        else
        {
            //throw new Exception(curl_error($ch));

            curl_close($ch);

            return array('status' => 400);
        }
    }
} 