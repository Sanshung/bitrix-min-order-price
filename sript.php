<?php

use Bitrix\Main;

$eventManager = Main\EventManager::getInstance();
$eventManager->addEventHandler("sale", "OnSaleOrderBeforeSaved", "MinPriceOrderBeforeSaved");

function MinPriceOrderBeforeSaved(Bitrix\Main\Event $event)
{


    $result = new \Bitrix\Main\Entity\EventResult(\Bitrix\Main\EventResult::SUCCESS);

    $order = $event->getParameter('ENTITY');

    if ($order instanceof \Bitrix\Sale\Order && !defined('ADMIN_SECTION')) {

        
        global $gParams;

        Main\Loader::includeModule('iblock');

        $deliveryId = $order->getField('DELIVERY_ID'); //дотсавка 
        

        
        $sum = $order->getPrice();

        $res = CIBlockElement::GetProperty(5, $gParams['city_id'], "sort", "asc", array("CODE" => "MIN_SUMM")); //мин цена из иб
        if ($ob = $res->GetNext()) {

            $minSum = $ob['VALUE'];
            

            if ($minSum > $sum && $deliveryId == 2) {
               return new \Bitrix\Main\EventResult(
                    \Bitrix\Main\EventResult::ERROR,
                    new \Bitrix\Sale\ResultError('Минимальная сумма заказа для самовывоза ' . $minSum, 'code'),
                    'sale');

            }

        }
    }

    return $result;
}


?>
