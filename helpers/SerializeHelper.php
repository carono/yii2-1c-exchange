<?php


namespace carono\exchange1c\helpers;


use carono\exchange1c\interfaces\DocumentInterface;
use carono\exchange1c\interfaces\OfferInterface;
use carono\exchange1c\interfaces\PartnerInterface;

class SerializeHelper
{
    /**
     * @param PartnerInterface $partner
     * @return \SimpleXMLElement
     */
    public static function serializePartner(PartnerInterface $partner)
    {
        $xml = new \SimpleXMLElement('<Контрагент></Контрагент>');
        self::addFields($xml, $partner, $partner->getExportFields1c());
        return $xml;
    }

    /**
     * @param OfferInterface $offer
     * @param DocumentInterface $document
     * @return \SimpleXMLElement
     */
    public static function serializeOffer(OfferInterface $offer, DocumentInterface $document)
    {
        $productNode = new \SimpleXMLElement('<Товар></Товар>');
        self::addFields($productNode, $offer, $offer->getExportFields1c($document));
        if ($group = $offer->getGroup1c()) {
            $productNode->addChild('ИдКаталога', $group->{$group->getIdFieldName1c()});
        }
        return $productNode;
    }

    /**
     * @param DocumentInterface $document
     * @return \SimpleXMLElement
     */
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

    /**
     * @param $node
     * @param $object
     * @param $fields
     */
    public static function addFields($node, $object, $fields)
    {
        foreach ($fields as $field => $value) {
            NodeHelper::addChild($node, $object, $field, $value);
        }
    }

}