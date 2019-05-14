<#1>
<?php
if(!$ilDB->tableExists('powbi_prov_index'))
{
	$ilDB->createTable('powbi_prov_index', [
		'id' => [
			'type'     => 'integer',
			'length'   => 4,
			'notnull' => true,
			'default' => 0,
		],
		'processed' => [
			'type'     => 'integer',
			'length'   => 4,
			'notnull' => true,
			'default' => 0,
		],
		'trigger' => [
			'type' => 'text',
			'notnull' => true,
			'default' => '',
		],
		'timestamp' => [
			'type'     => 'integer',
			'length'   => 4,
			'notnull'  => true,
		],
	]);
	$ilDB->addPrimaryKey('lerq_queue', array('id'));
	$ilDB->createSequence('lerq_queue');
}

?>
