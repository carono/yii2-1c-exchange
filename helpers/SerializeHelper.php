<?php


namespace carono\exchange1c\helpers;


use carono\exchange1c\interfaces\DocumentInterface;
use carono\exchange1c\interfaces\OfferInterface;
use carono\exchange1c\interfaces\PartnerInterface;
use carono\exchange1c\interfaces\ProductInterface;

class SerializeHelper
{
    public static function serializePartner(PartnerInterface $partner)
    {
        $xml = new \SimpleXMLElement('<Контрагент></Контрагент>');
        self::addFields($xml, $partner, $partner->getExportFields1c());
        foreach ($partner::getFields1c() as $field1c => $attribute) {
            if ($attribute) {
                $xml->addChild($field1c, $partner->{$attribute});
            }
        }
        return $xml;
    }

    public static function serializeOffer(OfferInterface $offer, DocumentInterface $document)
    {
        $productNode = new \SimpleXMLElement('<Товар></Товар>');
        self::addFields($productNode, $offer, $offer->getExportFields1c($document));
        $productNode->addChild('ИдКаталога', $offer->getGroup1c()->getId1c());
        return $productNode;
    }


    public static function serializeDocument(DocumentInterface $document)
    {
        $documentNode = new \SimpleXMLElement('<Документ></Документ>');

        self::addFields($documentNode, $document, $document->getExportFields1c());

        $partnersNode = $documentNode->addChild('Контрагенты');
        $partner = $document->getPartner1c();

        $partnerNode = self::serializePartner($partner);
        NodeHelper::appendNode($partnersNode, $partnerNode);
        $products = $documentNode->addChild('Товары');
        foreach ($document->getOffers1c() as $offer) {
            $productNode = self::serializeOffer($offer, $document);
            NodeHelper::appendNode($products, $productNode);
        }
        return $documentNode;
    }

    public static function addFields($node, $object, $fields)
    {
        foreach ($fields as $field => $value) {
            NodeHelper::addChild($node, $object, $field, $value);
        }
    }

}