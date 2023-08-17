<?php

namespace Sync\Entity;

/**
 * Interface EntityServiceInterface
 *
 * @package Sync\Entity\
 */
interface EntityServiceInterface
{
/**
* Получить список всех сущностейю
*
* @param array $queryParams Входные параметры GET-запроса
* @return object JSON-Обьект всех данных по сущности
*/
public function get(array $queryParams): object;

/**
* Добавить сущность.
*
* @param string $name Имя новой сущности
* @return object JSON-Обьект обновленных данны по сущности
*/
public function add(string $name): object;

/**
* Добавить несколько сущностей.
*
* @return object JSON-Обьект обновленных данных по сущности.
*/
public function addSome(array $names): object;
}
