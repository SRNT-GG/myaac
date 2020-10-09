<?php
/**
 * Project: MyAAC
 *     Automatic Account Creator for Open Tibia Servers
 *
 * This is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2020 MyAAC
 * @link      https://my-aac.org
 */

/**
 * Class DataLoader
 */
class DataLoader
{
	private static $locale;
	private static $startTime;

	/**
	 * Load data from server
	 */
	public static function load()
	{
		self::$startTime = microtime(true);

		require LIBS . 'items.php';
		if(Items::loadFromXML()) {
			success(self::$locale['step_database_loaded_items'] . self::getLoadedTime());
		}
		else {
			error(Items::getError());
		}

		self::$startTime = microtime(true);

		require LIBS . 'creatures.php';
		if(Creatures::loadFromXML()) {
			success(self::$locale['step_database_loaded_monsters'] . self::getLoadedTime());

			if(Creatures::getMonstersList()->hasErrors()) {
				self::$locale['step_database_error_monsters'] = str_replace('$LOG$', 'system/logs/error.log', self::$locale['step_database_error_monsters']);
				warning(self::$locale['step_database_error_monsters']);
			}
		}
		else {
			error(Creatures::getLastError());
		}

		self::$startTime = microtime(true);

		require LIBS . 'spells.php';
		if(Spells::loadFromXML()) {
			success(self::$locale['step_database_loaded_spells'] . self::getLoadedTime());
		}
		else {
			error(Spells::getLastError());
		}

		self::$startTime = microtime(true);

		if (Towns::save()) {
			success(self::$locale['step_database_loaded_towns'] . self::getLoadedTime());
		}
		else {
			warning(self::$locale['step_database_error_towns']);
		}

		self::$startTime = microtime(true);

		require LIBS . 'weapons.php';
		if(Weapons::loadFromXML()) {
			success(self::$locale['step_database_loaded_weapons'] . self::getLoadedTime());
		}
		else {
			error(Weapons::getError());
		}
	}

	public static function setLocale($locale) {
		self::$locale = $locale;
	}

	private static function getLoadedTime()
	{
		$endTime = round(microtime(true) - self::$startTime, 3);
		return ' (' . str_replace('$TIME$', $endTime, self::$locale['loaded_in_ms']) . ')';
	}
}