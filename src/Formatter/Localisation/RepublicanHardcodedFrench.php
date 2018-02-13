<?php

namespace Popy\RepublicanCalendar\Formatter\Localisation;

use Popy\Calendar\Formatter\LocalisationInterface;

class RepublicanHardcodedFrench implements LocalisationInterface
{
    /**
    * {@inheritDoc}
    */
    public function getMonthName($month)
    {
        $names = array(
            'Vendémiaire',
            'Brumaire',
            'Frimaire',
            'Nivôse',
            'Pluviôse',
            'Ventôse',
            'Germinal',
            'Floréal',
            'Prairial',
            'Messidor',
            'Thermidor',
            'Fructidor',
            'Sans-culottides',
       );

        if (isset($names[$month])) {
            return $names[$month];
        }
    }

    /**
    * {@inheritDoc}
    */
    public function getMonthShortName($month)
    {
        if (null === $name = $this->getMonthName($month)) {
            return;
        }

        // SHould use a multibyte string handler, but there is no such character
        // in the 3 first chars of month names, and yes, it's a ugly hardcoded
        // Localisation file.
        return substr($name, 0, 3);
    }

    /**
    * {@inheritDoc}
    */
    public function getDayName($day)
    {
        $names = array(
            'Primidi',
            'Duodi',
            'Tridi',
            'Quartidi',
            'Quintidi',
            'Sextidi',
            'Septidi',
            'Octidi',
            'Nonidi',
            'Décadi',
        );

        $type = substr($day, 0, 1);
        $day = strpos('0123456789', $type) === false ? substr($day, 1) : $day;

        if ($type === 'y') {
            return $this->getIndividualDayName($day);
        }

        if (isset($names[$day])) {
            return $names[$day];
        }
    }

    /**
    * {@inheritDoc}
    */
    public function getDayShortName($day)
    {
        if (null === $name = $this->getDayName($day)) {
            return;
        }

        if (function_exists('mb_substr')) {
            return mb_substr($name, 0, 3, 'UTF-8');
        }

        return substr($name, 0, 3);
    }

    /**
    * Get days individual name by day index
    *
    * @param integer $day
    *
    * @return  string|null
    */
    public function getIndividualDayName($day)
    {
        $names = array(
            'Raisin',
            'Safran',
            'Châtaigne',
            'Colchique',
            'Cheval',
            'Balsamine',
            'Carotte',
            'Amarante',
            'Panais',
            'Cuve',
            'Pomme de terre',
            'Immortelle',
            'Potiron',
            'Réséda',
            'Âne',
            'Belle de nuit',
            'Citrouille',
            'Sarrasin',
            'Tournesol',
            'Pressoir',
            'Chanvre',
            'Pêche',
            'Navet',
            'Amaryllis',
            'Bœuf',
            'Aubergine',
            'Piment',
            'Tomate',
            'Orge',
            'Tonneau',
            'Pomme',
            'Céleri',
            'Poire',
            'Betterave',
            'Oie',
            'Héliotrope',
            'Figue',
            'Scorsonère',
            'Alisier',
            'Charrue',
            'Salsifis',
            'Mâcre',
            'Topinambour',
            'Endive',
            'Dindon',
            'Chervis',
            'Cresson',
            'Dentelaire',
            'Grenade',
            'Herse',
            'Bacchante',
            'Azerole',
            'Garance',
            'Orange',
            'Faisan',
            'Pistache',
            'Macjonc',
            'Coing',
            'Cormier',
            'Rouleau',
            'Raiponce',
            'Turneps',
            'Chicorée',
            'Nèfle',
            'Cochon',
            'Mâche',
            'Chou-fleur',
            'Miel',
            'Genièvre',
            'Pioche',
            'Cire',
            'Raifort',
            'Cèdre',
            'Sapin',
            'Chevreuil',
            'Ajonc',
            'Cyprès',
            'Lierre',
            'Sabine',
            'Hoyau',
            'Érable sucré',
            'Bruyère',
            'Roseau',
            'Oseille',
            'Grillon',
            'Pignon',
            'Liège',
            'Truffe',
            'Olive',
            'Pelle',
            'Tourbe',
            'Houille',
            'Bitume',
            'Soufre',
            'Chien',
            'Lave',
            'Terre végétale',
            'Fumier',
            'Salpêtre',
            'Fléau',
            'Granit',
            'Argile',
            'Ardoise',
            'Grès',
            'Lapin',
            'Silex',
            'Marne',
            'Pierre à chaux',
            'Marbre',
            'Van',
            'Pierre à plâtre',
            'Sel',
            'Fer',
            'Cuivre',
            'Chat',
            'Étain',
            'Plomb',
            'Zinc',
            'Mercure',
            'Crible',
            'Lauréole',
            'Mousse',
            'Fragon',
            'Perce-neige',
            'Taureau',
            'Laurier tin',
            'Amadouvier',
            'Mézéréon',
            'Peuplier',
            'Cognée',
            'Ellébore',
            'Brocoli',
            'Laurier',
            'Avelinier',
            'Vache',
            'Buis',
            'Lichen',
            'If',
            'Pulmonaire',
            'Serpette',
            'Thlaspi',
            'Thimele',
            'Chiendent',
            'Trainasse',
            'Lièvre',
            'Guède',
            'Noisetier',
            'Cyclamen',
            'Chélidoine',
            'Traîneau',
            'Tussilage',
            'Cornouiller',
            'Violier',
            'Troène',
            'Bouc',
            'Asaret',
            'Alaterne',
            'Violette',
            'Marceau',
            'Bêche',
            'Narcisse',
            'Orme',
            'Fumeterre',
            'Vélar',
            'Chèvre',
            'Épinard',
            'Doronic',
            'Mouron',
            'Cerfeuil',
            'Cordeau',
            'Mandragore',
            'Persil',
            'Cochléaria',
            'Pâquerette',
            'Thon',
            'Pissenlit',
            'Sylvie',
            'Capillaire',
            'Frêne',
            'Plantoir',
            'Primevère',
            'Platane',
            'Asperge',
            'Tulipe',
            'Poule',
            'Bette',
            'Bouleau',
            'Jonquille',
            'Aulne',
            'Greffoir',
            'Pervenche',
            'Charme',
            'Morille',
            'Hêtre',
            'Abeille',
            'Laitue',
            'Mélèze',
            'Ciguë',
            'Radis',
            'Ruche',
            'Gainier',
            'Romaine',
            'Marronnier',
            'Roquette',
            'Pigeon',
            'Lilas (commun)',
            'Anémone',
            'Pensée',
            'Myrtile',
            'Couvoir',
            'Rose',
            'Chêne',
            'Fougère',
            'Aubépine',
            'Rossignol',
            'Ancolie',
            'Muguet',
            'Champignon',
            'Hyacinthe',
            'Râteau',
            'Rhubarbe',
            'Sainfoin',
            'Bâton-d\'or',
            'Chamérisier',
            'Ver à soie',
            'Consoude',
            'Pimprenelle',
            'Corbeille d\'or',
            'Arroche',
            'Sarcloir',
            'Statice',
            'Fritillaire',
            'Bourrache',
            'Valériane',
            'Carpe',
            'Fusain',
            'Civette',
            'Buglosse',
            'Sénevé',
            'Houlette',
            'Luzerne',
            'Hémérocalle',
            'Trèfle',
            'Angélique',
            'Canard',
            'Mélisse',
            'Fromental',
            'Lis martagon',
            'Serpolet',
            'Faux',
            'Fraise',
            'Bétoine',
            'Pois',
            'Acacia',
            'Caille',
            'Œillet',
            'Sureau',
            'Pavot',
            'Tilleul',
            'Fourche',
            'Barbeau',
            'Camomille',
            'Chèvrefeuille',
            'Caille-lait',
            'Tanche',
            'Jasmin',
            'Verveine',
            'Thym',
            'Pivoine',
            'Chariot',
            'Seigle',
            'Avoine',
            'Oignon',
            'Véronique',
            'Mulet',
            'Romarin',
            'Concombre',
            'Échalote',
            'Absinthe',
            'Faucille',
            'Coriandre',
            'Artichaut',
            'Girofle',
            'Lavande',
            'Chamois',
            'Tabac',
            'Groseille',
            'Gesse',
            'Cerise',
            'Parc',
            'Menthe',
            'Cumin',
            'Haricot',
            'Orcanète',
            'Pintade',
            'Sauge',
            'Ail',
            'Vesce',
            'Blé',
            'Chalemie',
            'Épeautre',
            'Bouillon-blanc',
            'Melon',
            'Ivraie',
            'Bélier',
            'Prêle',
            'Armoise',
            'Carthame',
            'Mûre',
            'Arrosoir',
            'Panic',
            'Salicorne',
            'Abricot',
            'Basilic',
            'Brebis',
            'Guimauve',
            'Lin',
            'Amande',
            'Gentiane',
            'Écluse',
            'Carline',
            'Câprier',
            'Lentille',
            'Aunée',
            'Loutre',
            'Myrte',
            'Colza',
            'Lupin',
            'Coton',
            'Moulin',
            'Prune',
            'Millet',
            'Lycoperdon',
            'Escourgeon',
            'Saumon',
            'Tubéreuse',
            'Sucrion',
            'Apocyn',
            'Réglisse',
            'Échelle',
            'Pastèque',
            'Fenouil',
            'Épine vinette',
            'Noix',
            'Truite',
            'Citron',
            'Cardère',
            'Nerprun',
            'Tagette',
            'Hotte',
            'Églantier',
            'Noisette',
            'Houblon',
            'Sorgho',
            'Écrevisse',
            'Bigarade',
            'Verge d\'or',
            'Maïs',
            'Marron',
            'Panier',
            'jour de la vertu',
            'jour du génie',
            'jour du travail',
            'jour de l\'opinion',
            'jour des récompenses',
            'jour de la révolution',
            );

        if (isset($names[$day])) {
            return $names[$day];
        }
    }

    /**
    * {@inheritDoc}
    */
    public function getNumberOrdinalSuffix($number)
    {
        if ($number == 1) {
            return 'er';
        }

        return 'e';
    }
}
