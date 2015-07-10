<?php
class Neuron_GameServer_Mappers_ClassMapper
{
	private static function getClasses ($reload = false)
	{
		static $classes;

		if (!isset ($classes) || $reload)
		{
			$db = Neuron_DB_Database::getInstance ();

			$ids = array ();
			$names = array ();

			$chk = $db->query 
			("
				SELECT
					*
				FROM
					n_classes
			");

			foreach ($chk as $v)
			{
				$ids[$v['ac_id']] = $v['ac_name'];
				$names[$v['ac_name']] = $v['ac_id'];
			}

			$classes = array ('ids' => $ids, 'names' => $names);
		}

		return $classes;
	}

	public static function getId (Andromeda_Interfaces_Identifiable $object)
	{
		static $classnames;

		if (!isset ($classnames))
		{
			$classes = self::getClasses ();
			$classnames = $classes['names'];
		}

		$class = get_class ($object);

		if (!isset ($classnames[$class]))
		{
			$db = Neuron_DB_Database::getInstance ();

			$db->query 
			("
				INSERT INTO
					n_classes
				SET
					ac_name = '{$db->escape ($class)}'
			");

			$classes = self::getClasses (true);
			$classnames = $classes['names'];
		}

		return $classnames[$class];
	}

	public static function getClass ($classname)
	{
		static $classes;

		if (!isset ($classnames))
		{
			$classesRaw = self::getClasses ();
			$classnames = $classesRaw['ids'];
		}

		if (!isset ($classnames[$classname]))
		{
			throw new Exception ("Class not found in IDMapper.");
		}

		return $classnames[$classname];
	}
}