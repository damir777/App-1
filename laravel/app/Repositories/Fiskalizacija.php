<?php

namespace App\Repositories;

class Fiskalizacija
{
    private $cis;
    private $XMLRequest;
    private $payload;

    public function Fiskaliziraj($fiscal_data, $is_reversed = false)
    {
        //učitavam certifikate
        $this->cis = new PU_CIS($fiscal_data);

        //kreiram request
        $racunRequest = $this->cis->CreateRacunRequest($fiscal_data, $is_reversed);
        $this->XMLRequest = $racunRequest['xml'];
        
        //set zki
        $zki = $racunRequest['zki'];
		
        //potpisujem certifikatom request
        $this->cis->PotpisivanjeXMLRequesta($this->XMLRequest);
		
        //posto u objektu PU_CIS imam aktivan XMLRequestDOMDoc i on ce se koristiti u AddSoapMessage
        //dodajem SOAP envelope na potpisani XML
        $this->payload = $this->cis->AddSoapMessage('RacunZahtjev');

        //saljem u poreznu
        $this->cis->SendRequest($this->payload);

        return array('status' => $this->cis->finaResponse, 'zki' => $zki, 'jir' => $this->cis->Jir);
        //ako je sve ok u field-u jir u objektu PU_CIS se nalazi jir, u protivnom  baca exception
    }
} 