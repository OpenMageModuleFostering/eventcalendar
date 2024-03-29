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
 * Events admin controller
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Adminhtml_Events_Events_CommentController
    extends Mage_Adminhtml_Controller_Action {
    /**
     * init the comment
     * @access protected
     * @return Ultimate_Events_Model_Events_Comment
     * @author Ultimate Module Creator
     */
    protected function _initComment(){
        $commentId  = (int) $this->getRequest()->getParam('id');
        $comment    = Mage::getModel('ultimate_events/events_comment');
        if ($commentId) {
            $comment->load($commentId);
        }
        Mage::register('current_comment', $comment);
        return $comment;
    }
     /**
     * default action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function indexAction() {
        $this->loadLayout();
        $this->_title(Mage::helper('ultimate_events')->__('Events'))
             ->_title(Mage::helper('ultimate_events')->__('Events'))
             ->_title(Mage::helper('ultimate_events')->__('Comments'));
        $this->renderLayout();
    }
    /**
     * grid action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function gridAction() {
        $this->loadLayout()->renderLayout();
    }
    /**
     * edit comment - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function editAction() {
        $commentId    = $this->getRequest()->getParam('id');
        $comment      = $this->_initComment();
        if (!$comment->getId()) {
            $this->_getSession()->addError(Mage::helper('ultimate_events')->__('This comment no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $comment->setData($data);
        }
        Mage::register('comment_data', $comment);
        $events = Mage::getModel('ultimate_events/events')->load($comment->getEventsId());
        Mage::register('current_events', $events);
        $this->loadLayout();
        $this->_title(Mage::helper('ultimate_events')->__('Events'))
             ->_title(Mage::helper('ultimate_events')->__('Events'))
             ->_title(Mage::helper('ultimate_events')->__('Comments'))
             ->_title($comment->getTitle());
        $this->renderLayout();
    }
    /**
     * save events - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost('comment')) {
            try {
                $comment = $this->_initComment();
                $comment->addData($data);
                if (!$comment->getCustomerId()){
                    $comment->unsCustomerId();
                }
                $comment->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ultimate_events')->__('Comment was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $comment->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('There was a problem saving the comment.'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('Unable to find comment to save.'));
        $this->_redirect('*/*/');
    }
    /**
     * delete comment - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0) {
            try {
                $comment = Mage::getModel('ultimate_events/events_comment');
                $comment->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ultimate_events')->__('Comment was successfully deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('There was an error deleting the comment.'));
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('Could not find comment to delete.'));
        $this->_redirect('*/*/');
    }
    /**
     * mass delete comments - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massDeleteAction() {
        $commentIds = $this->getRequest()->getParam('comment');
        if(!is_array($commentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('Please select comments to delete.'));
        }
        else {
            try {
                foreach ($commentIds as $commentId) {
                    $comment = Mage::getModel('ultimate_events/events_comment');
                    $comment->setId($commentId)->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ultimate_events')->__('Total of %d comments were successfully deleted.', count($commentIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('There was an error deleting comments.'));
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * mass status change - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function massStatusAction(){
        $commentIds = $this->getRequest()->getParam('comment');
        if(!is_array($commentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('Please select comments.'));
        }
        else {
            try {
                foreach ($commentIds as $commentId) {
                    $comment = Mage::getSingleton('ultimate_events/events_comment')->load($commentId)
                             ->setStatus($this->getRequest()->getParam('status'))
                             ->setIsMassupdate(true)
                             ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d comments were successfully updated.', count($commentIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ultimate_events')->__('There was an error updating comments.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    /**
     * export as csv - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportCsvAction(){
        $fileName   = 'events_comments.csv';
        $content    = $this->getLayout()->createBlock('ultimate_events/adminhtml_events_comment_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as MsExcel - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportExcelAction(){
        $fileName   = 'events_comments.xls';
        $content    = $this->getLayout()->createBlock('ultimate_events/adminhtml_events_comment_grid')->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * export as xml - action
     * @access public
     * @return void
     * @author Ultimate Module Creator
     */
    public function exportXmlAction(){
        $fileName   = 'events_comments.xml';
        $content    = $this->getLayout()->createBlock('ultimate_events/adminhtml_events_comment_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    /**
     * check access
     * @access protected
     * @return bool
     * @author Ultimate Module Creator
     */
    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('cms/ultimate_events/events_comments');
    }
}
