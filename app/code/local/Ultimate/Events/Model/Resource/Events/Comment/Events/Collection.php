<?php
/**
 * Ultimate_Events extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Ultimate
 * @package        Ultimate_Events
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Events comments resource collection model
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
    extends Ultimate_Events_Model_Resource_Events_Collection {
    /**
     * Entities alias
     *
     * @var array
     */
    protected $_entitiesAlias        = array();
    /**
     * Comment store table
     *
     * @var string
     */
    protected $_commentStoreTable;

    /**
     * Add store data flag
     *
     * @var boolean
     */
    protected $_addStoreDataFlag     = false;
    /**
     * Filter by stores for the collection
     *
     * @var array
     */
    protected $_storesIds           = array();
    /**
     * construct
     * @access protected
     * @author Ultimate Module Creator
     */
    protected function _construct() {
        $this->_init('ultimate_events/events');
        $this->_setIdFieldName('comment_id');
        $this->_commentStoreTable = Mage::getSingleton('core/resource')->getTableName('ultimate_events/events_comment_store');
    }
    /**
     * init select
     * @access protected
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    protected function _initSelect() {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }
    /**
     * Add customer filter
     * @access public
     * @param int $customerId
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    public function addCustomerFilter($customerId) {
        $this->getSelect()->where('ct.customer_id = ?', $customerId);
        return $this;
    }

    /**
     * Add entity filter
     * @access public
     * @param int $entityId
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    public function addEntityFilter($entityId) {
        $this->getSelect()->where('ct.events_id = ?', $entityId);
        return $this;
    }

    /**
     * Add status filter
     * @access public
     * @param mixed $status
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    public function addStatusFilter($status = 1) {
        $this->getSelect()->where('ct.status = ?', $status);
        return $this;
    }

    /**
     * Set date order
     * @access public
     * @param string $dir
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    public function setDateOrder($dir = 'DESC') {
        $this->setOrder('ct.created_at', $dir);
        return $this;
    }

    /**
     * join fields to entity
     * @accessprotected
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    protected function _joinFields() {
        $commentTable = Mage::getSingleton('core/resource')->getTableName('ultimate_events/events_comment');
        $this->getSelect()
            ->join(array('ct' => $commentTable),
                'ct.events_id = main_table.entity_id',
                array(
                    'ct_title'=>'title',
                    'ct_comment_id'=>'comment_id',
                    'ct_name'=> 'name',
                    'ct_status'=>'status',
                    'ct_email'=>'email',
                    'ct_created_at'=>'created_at',
                    'ct_updated_at'=>'updated_at'
                )
            );
        return $this;
    }

    /**
     * Retrive all ids for collection
     * @access public
     * @param mixed $limit
     * @param mixed $offset
     * @return array
     * @author Ultimate Module Creator
     */
    public function getAllIds($limit = null, $offset = null) {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('ct.comment_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }
    /**
     * Retrieves column values
     * @access public
     * @param string $colName
     * @return array
     * @author Ultimate Module Creator
     */
    public function getColumnValues($colName) {
        $col = array();
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }
        return $col;
    }
    /**
     * Render SQL for retrieve product count
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getSelectCountSql() {
        $select = parent::getSelectCountSql();
        $this->_applyStoresFilterToSelect($select);
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('COUNT(main_table.entity_id)')
            ->reset(Zend_Db_Select::HAVING);

        return $select;
    }
    /**
     * Adds store filter into array
     * @access public
     * @param mixed $storeId
     * @param bool $withAdmin
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    public function addStoreFilter($storeId = null, $withAdmin = true) {
        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }
        parent::addStoreFilter($storeId, $withAdmin);
        if (!is_array($storeId)) {
            $storeId = array($storeId);
        }
        if (!empty($this->_storesIds)) {
            $this->_storesIds = array_intersect($this->_storesIds, $storeId);
        } else {
            $this->_storesIds = $storeId;
        }

        return $this;
    }
    /**
     * Adds specific store id into array
     * @access public
     * @param array $storeId
     * @param bool $withAdmin
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    public function setStoreFilter($storeId, $withAdmin = false) {
        if (is_array($storeId) && isset($storeId['eq'])) {
            $storeId = array_shift($storeId);
        }

        if (!is_array($storeId)){
            $storeId = array($storeId);
        }

        if (!empty($this->_storesIds)){
            $this->_storesIds = array_intersect($this->_storesIds, $storeId);
        } else {
            $this->_storesIds = $storeId;
        }
        if ($withAdmin) {
            $this->_storesIds = array_merge($this->_storesIds, array(0));
        }
        return $this;
    }

    /**
     * Applies all store filters in one place to prevent multiple joins in select
     * @access protected
     * @param null|Zend_Db_Select $select
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    protected function _applyStoresFilterToSelect(Zend_Db_Select $select = null) {
        $adapter = $this->getConnection();
        $storesIds = $this->_storesIds;
        if (is_null($select)){
            $select = $this->getSelect();
        }

        if (is_array($storesIds) && (count($storesIds) == 1)) {
            $storesIds = array_shift($storesIds);
        }

        if (is_array($storesIds) && !empty($storesIds)) {
            $inCond = $adapter->prepareSqlCondition('store.store_id', array('in' => $storesIds));
            $select->join(array('store' => $this->_commentStoreTable),
                'ct.comment_id=store.comment_id AND ' . $inCond,
                array())
            ->group('ct.comment_id');

            $this->_useAnalyticFunction = true;
        } elseif (!empty($storesIds)) {
            $select->join(array('store' => $this->_commentStoreTable),
                $adapter->quoteInto('ct.comment_id=store.comment_id AND store.store_id = ?', (int)$storesIds),
                array());
        }
        return $this;
    }

    /**
     * Add stores data
     * @access public
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    public function addStoreData() {
        $this->_addStoreDataFlag = true;
        return $this;
    }
    /**
     * Action after load
     * @access protected
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    protected function _afterLoad() {
        parent::_afterLoad();
        if ($this->_addStoreDataFlag) {
            $this->_addStoreData();
        }
        return $this;
    }

    /**
     * Add store data
     * @access protected
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    protected function _addStoreData() {
        $adapter = $this->getConnection();
        $commentIds = $this->getColumnValues('ct_comment_id');
        $storesToComments = array();
        if (count($commentIds)>0) {
            $commentIdCondition = $this->_getConditionSql('comment_id', array('in' => $commentIds));
            $select = $adapter->select()
                ->from($this->_commentStoreTable)
                ->where($commentIdCondition);
            $result = $adapter->fetchAll($select);
            foreach ($result as $row) {
                if (!isset($storesToComments[$row['comment_id']])) {
                    $storesToComments[$row['comment_id']] = array();
                }
                $storesToComments[$row['comment_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($storesToComments[$item->getCtCommentId()])) {
                $item->setData('stores', $storesToComments[$item->getCtCommentId()]);
            } else {
                $item->setData('stores', array());
            }
        }
        return $this;
    }

    /**
     * Redeclare parent method for store filters applying
     * @access protected
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    //protected function _beforeLoad() {
      //  parent::_beforeLoad();
        //$this->_applyStoresFilterToSelect();
        //return $this;
    //}
    /**
     * Add attribute to filter
     * @access public
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return Ultimate_Events_Model_Resource_Events_Comment_Events_Collection
     * @author Ultimate Module Creator
     */
    public function addFieldToFilter($attribute, $condition = null, $joinType = 'inner') {
        switch($attribute) {
            case 'ct.comment_id':
            case 'ct.created_at':
            case 'ct.status':
            case 'ct.title':
            case 'ct.name':
            case 'ct.email':
            case 'ct.comment':
            case 'ct.updated_at':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                break;

            case 'stores':
                $this->setStoreFilter($condition);
                break;
            default:
                parent::addFieldToFilter($attribute, $condition, $joinType);
                break;
        }
        return $this;
    }
}
