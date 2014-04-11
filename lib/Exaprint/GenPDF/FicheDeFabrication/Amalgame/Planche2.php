<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/04/2014
 * Time: 16:48
 */

namespace Exaprint\GenPDF\FicheDeFabrication\Amalgame;


use Exaprint\GenPDF\FicheDeFabrication\Amalgame\Planche\Compteur;
use Exaprint\GenPDF\FicheDeFabrication\Amalgame\Planche\Compteurs;
use Exaprint\GenPDF\FicheDeFabrication\Amalgame\Planche\DetailSousTraitance;
use Exaprint\GenPDF\FicheDeFabrication\Amalgame\Planche\Faconnage;
use Exaprint\GenPDF\FicheDeFabrication\Amalgame\Planche\Finition;
use Exaprint\GenPDF\FicheDeFabrication\Amalgame\Planche\Finitions;
use Exaprint\GenPDF\FicheDeFabrication\Amalgame\Planche\Identification;
use Exaprint\GenPDF\FicheDeFabrication\Amalgame\Planche\Impression;
use Exaprint\GenPDF\FicheDeFabrication\Amalgame\Planche\IndicationsCommandes;
use Exaprint\GenPDF\FicheDeFabrication\Amalgame\Planche\Observations;
use Exaprint\TCPDF\Dimensions;
use Exaprint\TCPDF\Position;

class Planche2
{
    /** @var  array */
    public $planche;
    /** @var  Position */
    public $position;
    /** @var  \TCPDF */
    public $pdf;
    /** @var  Dimensions */
    public $dimensions;

    function __construct($dimensions, $pdf, $planche, $position)
    {
        $this->dimensions = $dimensions;
        $this->pdf        = $pdf;
        $this->planche    = $planche;
        $this->position   = $position;

        $this->identification();
        $this->impression();
        $this->finitions();
        $this->faconnage();
        $this->detailSousTraitance();
        $this->indicationsCommandes();
        $this->compteurs();
        $this->observations();
    }


    public function identification()
    {
        $identification = new Identification($this->planche);
        $identification->draw($this->pdf, $this->position->add(new Position(0, 0)));
    }

    public function impression()
    {
        $impression = new Impression($this->planche);
        $impression->draw($this->pdf, $this->position->add(new Position(0, 17)));

    }

    public function finitions()
    {
        $finitions = new Finitions($this->planche);
        $finitions->draw($this->pdf, $this->position->add(new Position(0, 33)));
    }

    public function faconnage()
    {
        $faconnage = new Faconnage($this->planche);
        $faconnage->draw($this->pdf, $this->position->add(new Position(100, 33)));
    }

    public function detailSousTraitance()
    {
        $detailST = new DetailSousTraitance($this->planche);
        $detailST->draw($this->pdf, $this->position->add(new Position(100, 49.5)));
    }

    public function indicationsCommandes()
    {
        $indications = new IndicationsCommandes($this->planche);
        $indications ->draw($this->pdf, $this->position->add(new Position(0, 66)));

    }

    public function observations()
    {
        $observations = new Observations($this->planche);
        $observations->draw($this->pdf, $this->position->add(new Position(100, 66)));
    }

    public function compteurs()
    {
        $compteurs = new Compteurs($this->planche);
        $compteurs->draw($this->pdf, $this->position->add(new Position(0, 120)));
    }

} 