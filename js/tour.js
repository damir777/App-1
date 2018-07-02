$(document).ready(function() {

    var steps = [{
            path: 'statistics',
            element: '#tour-one-step',
            title: 'Upoznajte MaliUred',
            content: 'Upoznajte MaliUred kroz kratku prezentaciju osnovnih funkcionalnosti.',
            placement: 'top'
        },
        {
            path: 'settings/taxGroups/add',
            element: '#tour-two-step',
            title: 'Kreiranje porezne grupe',
            content: 'Da bi unijeli proizvod/uslugu, potrebno je kreirati poreznu grupu.<br><br>' +
                'Upišite naziv porezne grupe i dodajte opcionalnu napomenu koja se ispisuje na dokumentima.',
            placement: 'bottom'
        },
        {
            path: 'settings/taxGroups/add',
            element: '#tour-three-step',
            title: 'Dodavanje poreza',
            content: 'Svaka porezna grupa mora imati poreznu stopu i datum od kada vrijedi.<br><br>Kada dođe do promjene poreza, ' +
                'potrebno je samo dodati novu poreznu stopu, a ne kreirati novu poreznu grupu.',
            placement: 'top'
        },
        {
            path: 'settings/offices/add',
            element: '#tour-four-step',
            title: 'Kreiranje poslovnice',
            content: 'Svi dokumenti, osim otpremnica, kreiraju se u nekoj poslovnici. Broj poslovnica nije ograničen.<br><br>' +
                'Oznaka poslovnice predstavlja drugi broj u broju nekog dokumenta (npr. 1/<b>1</b>/1).',
            placement: 'bottom'
        },
        {
            path: 'settings/registers/add',
            element: '#tour-five-step',
            title: 'Kreiranje naplatnog uređaja',
            content: 'Računi i ugovori se kreiraju na nekom naplatnom uređaju. Broj naplatnih uređaja nije ograničen.' +
                'Svaki naplatni uređaj se nalazi u nekoj poslovnici.<br><br>Oznaka naplatnog uređaja predstavlja treći broj u ' +
                'broju nekog dokumenta (npr. 1/1/<b>1</b>).',
            placement: 'bottom'
        },
        {
            path: 'docs/offers/add',
            element: '#tour-six-step',
            title: 'Dodavanje klijenta na dokument',
            content: 'Svakom dokumentu, osim maloprodajnom računu, mora se dodati klijent. On se dodaje pretraživanjem baze ' +
                'klijenata ili kreiranjem novog.',
            placement: 'right',
            onNext: function() {
                $('#addClient').modal('show');
            }
        },
        {
            path: 'docs/offers/add',
            element: '#tour-seven-step',
            title: 'Kreiranje novog klijenta',
            content: 'Obrazac za kreiranje novog klijenta se otvara klikom na Novi klijent.',
            placement: 'right',
            onPrev: function() {
                $('#addClient').modal('hide');
            },
            onNext: function() {
                $('#addClient').modal('hide');
            }
        },
        {
            path: 'docs/offers/add',
            element: '#tour-eight-step',
            title: 'Dodavanje proizvoda/usluge na dokument',
            content: 'Na svaki dokument se mora dodati proizvod/usluga. On se dodaje pretraživanjem baze ' +
                'proizvoda/usluga ili kreiranjem novog.',
            placement: 'right',
            onPrev: function() {
                $('#addClient').modal('show');
            },
            onNext: function() {
                $('#addProduct').modal('show');
            }
        },
        {
            path: 'docs/offers/add',
            element: '#tour-nine-step',
            title: 'Kreiranje novog proizvoda',
            content: 'Obrazac za kreiranje novog proizvoda se otvara klikom na Novi proizvod.',
            placement: 'right',
            onPrev: function() {
                $('#addProduct').modal('hide');
            },
            onNext: function() {
                $('#addProduct').modal('hide');
            }
        },
        {
            path: 'docs/offers/add',
            element: '#tour-ten-step',
            title: 'Ostale opcije na ponudi',
            content: 'U odjeljku Ostale opcije možete mijenjati jezik ponude i valutu.<br><br>Promjenim valute radi se ' +
                'automatska konverzija cijena i iznosa u odabranu valutu.',
            placement: 'top',
            onPrev: function() {
                $('#addProduct').modal('show');
            }
        },
        {
            path: 'docs/invoices/2/list',
            element: '#tour-eleven-step',
            title: 'Računi',
            content: 'Računi su podijeljeni na maloprodajne i veleprodajne. Maloprodajni računi se automatski fiskaliziraju. ' +
                '<br><br>Svi računi se mogu stornirati, a veleprodajni i brisati.',
            placement: 'bottom'
        },
        {
            path: 'docs/invoices/2/add',
            element: '#tour-twelve-step',
            title: 'Ostale opcije računa',
            content: 'U odjeljku Ostale opcije možete mijenjati jezik računa, valutu, valutu unosa itd.<br><br>Promjenim valute ' +
                'radi se automatska konverzija cijena i iznosa u odabranu valutu.<br><br>Model i poziv na broj se automatski ' +
                'kreiraju, ali se mogu ručno promijeniti.',
            placement: 'top'
        },
        {
            path: 'settings/fiscalCertificate/info',
            element: '#tour-thirteen-step',
            title: 'Fiskalizacija',
            content: 'Da bi izdavali maloprodajne račune, potrebno je od FINA-e zatražiti fiskalni certifikat i lozinku.<br><br>' +
                'Certifikat je potrebno dodati u MaliUred i vaši računi su spremni za fiskalizaciju.',
            placement: 'bottom'
        },
        {
            path: 'statistics',
            element: '#tour-fourteen-step',
            title: 'Statistika',
            content: 'Na ovom grafu možete pratiti statistiku ponuda i računa za tekuću godinu.',
            placement: 'top'
        },
        {
            path: 'statistics',
            element: '#tour-fifteen-step',
            title: 'Upute',
            content: 'Ovo je bila samo kratka prezentacija MalogUreda. Za detaljnije upute posjetite ' +
                '<a href="http://maliured/upute.html" target="_blank">Upute</a>',
            placement: 'bottom'
        }
    ];

    var tour = new Tour({
        steps: steps,
        debug: true,
        basePath: '/',
        template: '<div class="popover tour">' +
            '<div class="arrow"></div>' +
            '<h3 class="popover-title"></h3>' +
            '<div class="popover-content"></div>' +
            '<div class="popover-navigation">' +
                '<div class="btn-group">' +
                    '<button class="btn btn-sm btn-default tour-btn" data-role="prev">Nazad</button>' +
                    '<button class="btn btn-sm btn-default tour-btn" data-role="next">Naprijed</button>' +
                '</div>' +
                '<button class="btn btn-sm btn-default tour-btn" data-role="end">Završi</button>' +
            '</div>' +
        '</div>'
    });

    tour.init();

    $('.start-tour').click(function() {

        tour.restart();
    })
});