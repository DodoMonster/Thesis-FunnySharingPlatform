<?php

/**
 * openim应用消息
 * @author auto create
 */
class EsMessage
{
	
	/** 
	 * 消息内容
	 **/
	public $content;
	
	/** 
	 * 发送方
	 **/
	public $fromId;
	
	/** 
	 * 消息时间，UTC时间
	 **/
	public $time;
	
	/** 
	 * 接收方
	 **/
	public $toId;
	
	/** 
	 * 消息类型
	 **/
	public $type;
	
	/** 
	 * 消息UUID
	 **/
	public $uuid;	
}
?>