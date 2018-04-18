<?php

/**
 * Добавляется новый столбец 'Последний комментарий' в стандартный компонент списка
 *
 * Этот способ перегрузки стандартного компонента описан Kryachek Mikhail в комментарии
 * https://dev.1c-bitrix.ru/community/blogs/dev_bx/embedded-in-the-interface-box-bitrix24.php#106387
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/**
 * Bitrix vars
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global CDatabase $DB
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 */

$this->__file = '/bitrix/components/bitrix/crm.company.list/templates/.default/template.php';
$this->__folder = '/bitrix/components/bitrix/crm.company.list/templates/.default';

// Добавить новый столбец 'Последний комментарий'
$arResult['HEADERS'][] = [
    'id' => 'SAKHNOW_VIRTUAL_COMMENT',
    'name' => 'Последний комментарий',
    'sort' => false,
    'editable' => false,
];

// Заполнить значениями новый столбец 'Последний комментарий'
foreach ($arResult['COMPANY'] as $id => &$company) {
    $result = \Bitrix\Crm\Timeline\Entity\TimelineTable::query()
        ->addSelect('COMMENT')
        ->registerRuntimeField('binding', [
            'data_type' => '\Bitrix\Crm\Timeline\Entity\TimelineBindingTable',
            'reference' => [
                '=this.ID' => 'ref.OWNER_ID',
            ],
            'join_type' => 'inner',
        ])
        ->where('binding.ENTITY_ID', $id)
        ->where('binding.ENTITY_TYPE_ID', \CCrmOwnerType::Company)
        ->whereNotNull('COMMENT')
        ->setOrder(['CREATED' => 'DESC'])
        ->setLimit(1)
        ->exec()
        ->fetch()
    ;
    $company['SAKHNOW_VIRTUAL_COMMENT'] = $result['COMMENT'];
}
