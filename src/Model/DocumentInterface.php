<?php


namespace Jlab\EpasRepository\Model;

/**
 * Interface DocumentInterface
 *
 * Methods common to ePAS documents including Applications and Permits.
 *
 * @package Jlab\EpasRepository\Model
 */
interface DocumentInterface
{
    /**
     * Get the url for viewing the model in ePAS.
     *
     * @return string
     */
    function url() : string;

    /**
     * Get the Document Number
     *
     * For ePAS applications and permits this would map to ApplicationNumber and PermitNumber respectively.
     *
     * @return string
     */
     function documentNumber() : string;

    /**
     * Get the type of document ("Permit Request", "Permit", etc.)
     * @return string
     */
    function typeName() : string;

    /**
     * Get the string representation of the document's state.
     * ex: "PermitCreated"
     * @return string
     */
    function stateName() : string;

    /**
     * Get the surpassRef field.
     * The surpassRef seems to be the ePAS equivalent of an ID column.
     * @return string
     */
    function surpassRef() : string;

    /**
     * Get the title of the permit
     * @return string
     */
    function title() : string;
}