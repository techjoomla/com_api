<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/
defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Supports a value from an external table
 */
class JFormFieldForeignKey extends JFormField {

    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'foreignkey';
    private $input_type;
    private $table;
    private $key_field;
    private $value_field;

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput() {

        //Assign field properties.
        //Type of input the field shows
        $this->input_type = $this->getAttribute('input_type');

        //Database Table
        $this->table = $this->getAttribute('table');

        //The field that the field will save on the database
        $this->key_field = $this->getAttribute('key_field');

        //The column that the field shows in the input
        $this->value_field = $this->getAttribute('value_field');
        // Initialize variables.
        $html = '';

        //Load all the field options
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
                ->select(
                        array(
                            $this->key_field,
                            $this->value_field
                        )
                )
                ->from($this->table);

        $db->setQuery($query);
        $results = $db->loadObjectList();

        $input_options = 'class="' . $this->getAttribute('class') . '"';

        //Depends of the type of input, the field will show a type or another
        switch ($this->input_type) {
            case 'list':
            default:
                $options = array();

                //Iterate through all the results
                foreach ($results as $result) {
                    $options[] = JHtml::_('select.option', $result->{$this->key_field}, $result->{$this->value_field});
                }

                $value = $this->value;

                //If the value is a string -> Only one result
                if (is_string($value)) {
                    $value = array($value);
                } else if (is_object($value)) { //If the value is an object, let's get its properties.
                    $value = get_object_vars($value);
                }

                //If the select is multiple
                if ($this->multiple) {
                    $input_options.= 'multiple="multiple"';
                } else {
                    array_unshift($options, JHtml::_('select.option', '', ''));
                }

                $html = JHtml::_('select.genericlist', $options, $this->name, $input_options, 'value', 'text', $value, $this->id);
                break;
        }
        return $html;
    }

    /**
     * Wrapper method for getting attributes from the form element
     * @param string $attr_name Attribute name
     * @param mixed  $default Optional value to return if attribute not found
     * @return mixed The value of the attribute if it exists, null otherwise
     */
    public function getAttribute($attr_name, $default = null) {
        if (!empty($this->element[$attr_name])) {
            return $this->element[$attr_name];
        } else {
            return $default;
        }
    }

}
