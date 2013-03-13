<?php

namespace Exaprint\GenPDF\Resources\DAO;

use RBM\ResultsCombinator\ResultsCombinator;
use RBM\SqlQuery\Renderer\SqlServer;
use RBM\SqlQuery\Select;

class PrintboxProject
{
    const INDENT_TOKEN      = ".";
    const GROUP_OPEN_TOKEN  = "(";
    const GROUP_CLOSE_TOKEN = ")";

    public function fetchFromId($IDCommande, $IDLangue = 1)
    {
        $select = "
            SELECT
                TBL_TL_COMMANDE_PRINTBOX.IDCommande AS [order.id],
                TBL_TL_COMMANDE_PRINTBOX.MontantTTC AS [project.ati_amount],
                TBL_TL_COMMANDE_PRINTBOX.MontantTVA AS [project.vat_amount],
                TBL_TL_COMMANDE_PRINTBOX.MontantHT AS [project.et_amount],
                TBL_TL_COMMANDE_PRINTBOX.IDClient AS [client.id],
                [client].RaisonSociale AS [client.company_name],
                [client].MailFacturation AS [client.email],
                [client].Adresse1 AS [client.address.line1],
                [client].Adresse2 AS [client.address.line2],
                [client].Adresse3 AS [client.address.line3],
                [client].CodePostal AS [client.address.postcode],
                [client].Ville AS [client.address.city],
                [client].NomContact AS [client.contact_name],
                [client].PrenomContact AS [client.contact_forename],
                TBL_COMMANDE.ReferenceClient AS [order.reference],
                TBL_COMMANDE.MontantTTC AS [order.ati_amount],
                TBL_COMMANDE.MontantTVA AS [order.vat_amount],
                TBL_COMMANDE.MontantHT AS [order.et_amount],
                TBL_PRODUIT_LIBELLE_FRONT_TRAD.LibelleTraduit AS [product.name],
                TBL_PRODUIT_FAMILLE_PRODUIT_DESIGNATION_TRADUCTION.LibelleTraduit AS [product.subfamily],
                TBL_PRODUIT_FAMILLE_ARTICLES_TRAD.LibelleTraduit AS [product.family],
                [pboxer].RaisonSociale AS [pboxer.company_name],
                [pboxer].MailFacturation AS [pboxer.email],
                [pboxer].Adresse1 AS [pboxer.address.line1],
                [pboxer].Adresse2 AS [pboxer.address.line2],
                [pboxer].Adresse3 AS [pboxer.address.line3],
                [pboxer].CodePostal AS [pboxer.address.postcode],
                [pboxer].Ville AS [pboxer.address.city],
                [pboxer].NomContact AS [pboxer.contact_name],
                [pboxer].PrenomContact AS [pboxer.contact_forename],
                TBL_FRAIS.IDFrais AS [order.fees.id],
                TBL_FRAIS.Quantite AS [order.fees.quantity],
                TBL_FRAIS.MontantUnitaireHT AS [order.fees.unit_amount],
                TBL_FRAIS.MontantTTC AS [order.fees.ati_amount],
                TBL_FRAIS.MontantHT AS [order.fees.et_amount],
                TBL_FRAIS.MontantTVA AS [order.fees.vat_amount],
                TBL_FRAIS_TYPE_FRAIS_TRAD.LibelleTraduit AS [order.fees.type],
                Options.IDProduitOption AS [order.product.options.id],
                Options.ProduitOption AS [order.product.options.label],
                CASE
                    WHEN (Options.ProduitOptionValeur IS NULL)
                    THEN CAST (Options.Valeur AS VARCHAR)
                    ELSE Options.ProduitOptionValeur
                END AS [order.product.options.value],
                Options.Sigle AS [order.product.options.unit]
            FROM
                TBL_TL_COMMANDE_PRINTBOX
            JOIN VUE_INFOS_CLIENT AS [client] ON ([client].IDClient = TBL_TL_COMMANDE_PRINTBOX.IDClient)
            JOIN TBL_COMMANDE ON (TBL_COMMANDE.IDCommande = TBL_TL_COMMANDE_PRINTBOX.IDCommande)
            JOIN TBL_COMMANDE_LIGNE ON (TBL_COMMANDE_LIGNE.IDCommande = TBL_COMMANDE.IDCommande) CROSS APPLY dbo.f_OptionsCommande (TBL_COMMANDE.IDCommande, $IDLangue) AS Options
            JOIN VUE_INFOS_CLIENT AS [pboxer] ON ([pboxer].IDClient = TBL_COMMANDE.IDClient)
            LEFT JOIN TBL_FRAIS ON (TBL_FRAIS.IDCommande = TBL_COMMANDE.IDCommande)
            JOIN TBL_PRODUIT ON (TBL_PRODUIT.IDProduit = TBL_COMMANDE_LIGNE.IDProduit)
            JOIN TBL_PRODUIT_LIBELLE_FRONT_TRAD ON (TBL_PRODUIT_LIBELLE_FRONT_TRAD.IDProduit = TBL_PRODUIT.IDProduit)
            AND (TBL_PRODUIT_LIBELLE_FRONT_TRAD.IDLangue = $IDLangue)
            JOIN TBL_PRODUIT_FAMILLE_PRODUIT ON (TBL_PRODUIT_FAMILLE_PRODUIT.IDProduitFamilleProduit = TBL_PRODUIT.IDProduitFamilleProduit)
            JOIN TBL_PRODUIT_FAMILLE_PRODUIT_DESIGNATION_TRADUCTION ON (TBL_PRODUIT_FAMILLE_PRODUIT_DESIGNATION_TRADUCTION.IDProduitFamilleProduit = TBL_PRODUIT_FAMILLE_PRODUIT.IDProduitFamilleProduit)
            AND (TBL_PRODUIT_FAMILLE_PRODUIT_DESIGNATION_TRADUCTION.IDLangue = $IDLangue)
            JOIN TBL_PRODUIT_FAMILLE_ARTICLES ON (TBL_PRODUIT_FAMILLE_ARTICLES.IDProduitFamilleArticles = TBL_PRODUIT_FAMILLE_PRODUIT.IDProduitFamilleArticles)
            JOIN TBL_PRODUIT_FAMILLE_ARTICLES_TRAD ON (TBL_PRODUIT_FAMILLE_ARTICLES_TRAD.IDProduitFamilleArticles = TBL_PRODUIT_FAMILLE_ARTICLES.IDProduitFamilleArticles)
            AND (TBL_PRODUIT_FAMILLE_ARTICLES_TRAD.IDLangue = $IDLangue)
            LEFT JOIN TBL_FRAIS_TYPE_FRAIS_TRAD ON (TBL_FRAIS_TYPE_FRAIS_TRAD.IDFraisTypeFrais = TBL_FRAIS.IDFraisTypeFrais)
            AND (TBL_FRAIS_TYPE_FRAIS_TRAD.IDLangue = $IDLangue)
            WHERE
                (TBL_TL_COMMANDE_PRINTBOX.IDCommande = $IDCommande)";

        $db   = new Database("test");


        if ($r = $db->query($select)) {
            $combinator = new ResultsCombinator();
            $data = $combinator->combine(
                $r->fetchAll(\PDO::FETCH_ASSOC),
                "order.id",
                array(
                    "order.fees" => "id",
                    "product.options" => "id"
                )
            );

            if(isset($data[$IDCommande])){
                return $data[$IDCommande];
            }
        }
        return null;
    }

}