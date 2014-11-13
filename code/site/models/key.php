<?php
/**
 * @package com_api
 * @copyright Copyright (C) 2009 2014 Techjoomla, Tekdi Technologies Pvt. Ltd. All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link http://techjoomla.com
 * Work derived from the original RESTful API by Techjoomla (https://github.com/techjoomla/Joomla-REST-API) 
 * and the com_api extension by Brian Edgerton (http://www.edgewebworks.com)
*/

defined('_JEXEC') or die;
jimport('joomla.application.component.model');

class ApiModelKey extends ApiModel
{
	public function __construct( $config = array() )
	{
		parent::__construct( $config );

		$app	= JFactory::getApplication();

		$id = $app->input->get( 'id', false,'INT' );

		if ( !$id ) {
			$cid = $app->input->post->get( 'cid', array(),'ARRAY' );

			$id = @$cid[0];
		}

		if ( $id ) {
			$this->setState('id', $id);
		}

	}

	public function getList()
	{
		$where = null;
		if($user_id	= $this->getState('user_id')) :
			$where = 'WHERE userid = '.$this->_db->Quote($user_id);
		endif;

		$query = "SELECT id, hash, domain, state, created "
				."FROM #__api_keys "
				.$where
				;
		$this->_db->setQuery($query);
		$tokens	= $this->_db->loadObjectList();
		return $tokens;
	}

	public function save($data) {

		$creator			= JFactory::getUser()->get('id');
		$table 				= JTable::getInstance('Key', 'ApiTable');

		$old	= JTable::getInstance('Key', 'ApiTable');
		if ($data['id']) :
			$old->load($data['id']);
		endif;

		if (!$table->bind($data)) :
			$this->setError($this->_db->getErrorMsg());
			return false;
		endif;

		$table->domain		= ($old->domain != $table->domain) ? $this->validateDomain($table->domain) : $table->domain;
		if ($table->domain === false) :
			return false;
		endif;

		$table->created		= gmdate("Y-m-d H:i:s");
		$table->created_by	= $creator;

		if (!$table->id && !$table->hash) :
			$table->hash		= $this->generateUniqueHash();
		endif;

		if (!$table->check()) :
			$this->setError($table->getError());
			return false;
		endif;

		if (!$table->store()) :
			$this->setError($table->getError());
			return false;
		endif;

		return $table;
	}

	public function getData() {

		$table = JTable::getInstance('Key', 'ApiTable');
		if ($this->getState('id', 0))
			$table->load($this->getState('id'));

		return $table;
	}
	/**
	 * Delete api key
	 *
	 */
	public function delete($cid) {
		if (is_array($cid)) :
			$where = "id IN (".implode(", ", $cid).")";
		else :
			$where = "id = ".(int)$cid;
		endif;

		$this->_db->setQuery("DELETE FROM #__api_keys WHERE ".$where);
		if (!$this->_db->query()) :
			$this->setError($this->_db->getErrorMsg());
			return false;
		endif;
		return true;
	}

	private function generateUniqueHash() {
		$seed	= $this->makeRandomSeed();
		$hash	= sha1(uniqid($seed.microtime()));

		$this->_db->setQuery('SELECT COUNT(*) FROM #__api_keys WHERE hash = "'.$hash.'"');
		$exists	= $this->_db->loadResult();

		if ($exists) :
			return $this->generateUniqueHash();
		else :
			return $hash;
		endif;
	}

	private function makeRandomSeed() {
		$string	= 'abcdefghijklmnopqrstuvwxyz';
		$alpha	= str_split($string.strtoupper($string));
		$last	= count($alpha)-1;

		$seed	= null;
		for ($i=0; $i<16; $i++) :
			$seed .= $alpha[mt_rand(0, $last)];
		endfor;
		return $seed;
	}

	public function validateDomain($domain) {

		$sanitized	= preg_replace('/(http|https|ftp):\/\//', '', $domain);

		if(!preg_match('/^([0-9a-z-_\.]+\.+[0-9a-z\.]+)|localhost$/i',$sanitized)) :
			$this->setError(JText::_('COM_API_INVALID_DOMAIN_MSG'));
			return false;
		elseif ($sanitized != 'localhost') :
			$this->_db->setQuery("SELECT COUNT(*) FROM #__api_keys WHERE domain = ".$this->_db->Quote($sanitized));
			$exists = $this->_db->loadResult();
			if ($exists > 0) :
				$this->setError(JText::_('COM_API_DUPLICATE_DOMAIN_MSG'));
				return false;
			endif;
		endif;

		return $sanitized;
	}

}
