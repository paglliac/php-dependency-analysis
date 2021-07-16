<?php


namespace Domain;


interface ShipInterface
{
    public function deliver(Cargo $cargo);
}