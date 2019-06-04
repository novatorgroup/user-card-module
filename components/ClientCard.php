<?php

namespace novatorgroup\usercard\components;

use SimpleXMLElement;

class ClientCard extends SimpleXMLElement
{
    /**
     * ФИО
     * @var string
     */
    public $Partner;

    /**
     * Серия и номер карты
     * @var string
     */
    public $Card;

    /**
     * @var string
     */
    public $Email;

    /**
     * Полное название карты
     * @var string
     */
    public $Name;

    /**
     * Сумма накоплений
     * @var float
     */
    public $Summa;

    /**
     * Полное название карты
     * @return string
     */
    public function getFullCardName(): string
    {
        if (empty($this->Name)) {
            if ((string)$this->Card == (string)$this->Partner) {
                $this->Name = (string)$this->Card;
            } else {
                $this->Name = $this->Card . ' ' . $this->Partner;
            }
        }

        return (string)$this->Name;
    }

    /**
     * ФИО клиента
     * @return string
     */
    public function getClientName(): string
    {
        if ($names = UserCard::extractClientName($this->Partner)) {
            return implode(' ', $names);
        }

        return trim((string)$this->Partner);
    }

    /**
     * E-mail клиента
     * @return string
     */
    public function getEmail(): string
    {
        return trim((string)$this->Email);
    }

    /**
     * Сумма накоплений
     * @return float
     */
    public function getMoney(): float
    {
        return (float)str_replace(',', '.', (string)$this->Summa);
    }
}