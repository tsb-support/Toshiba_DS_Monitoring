<?php
/**
 * Plugins.php
 *
 * -Description-
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package    LibreNMS
 * @link       http://librenms.org
 * @copyright  2016
 * @author
 */

namespace LibreNMS;

use App\Models\Plugin;

class Plugins
{

    private static $plugins;


    public static function start()
    {
        if (is_null(self::$plugins)) {
            self::$plugins = [];
            $plugin_dir = Config::get('plugin_dir');

            if (file_exists($plugin_dir)) {
                // $plugin_files = scandir($config['plugin_dir']);
                $plugin_files = Plugin::isActive()->get()->toArray();
                foreach ($plugin_files as $plugins) {
                    $plugin_info = pathinfo($plugin_dir.'/'.$plugins['plugin_name'].'/'.$plugins['plugin_name'].'.php');
                    if ($plugin_info['extension'] == 'php') {
                        if (is_file($plugin_dir.'/'.$plugins['plugin_name'].'/'.$plugins['plugin_name'].'.php')) {
                            self::load($plugin_dir.'/'.$plugins['plugin_name'].'/'.$plugins['plugin_name'].'.php', $plugin_info['filename']);
                        }
                    }
                }

                return true;
            }
        }

        return false;
    }//end start()


    public static function load($file, $pluginName)
    {
        include $file;
        $pluginFullName = 'LibreNMS\\Plugins\\' . $pluginName;
        if (class_exists($pluginFullName)) {
            $pluginName = $pluginFullName;
            $plugin = new $pluginFullName;
        } elseif (class_exists($pluginName)) {
            $plugin = new $pluginName;
        } else {
            return null;
        }
        $hooks  = get_class_methods($plugin);

        foreach ($hooks as $hookName) {
            if ($hookName{0} != '_') {
                self::$plugins[$hookName][] = $pluginName;
            }
        }

        return $plugin;
    }//end load()

    public static function countHooks($hook)
    {
        // count all plugins implementing a specific hook
        self::start();
        if (!empty(self::$plugins[$hook])) {
            return count(self::$plugins[$hook]);
        } else {
            return false;
        }
    }

    public static function call($hook, $params = false)
    {
        self::start();

        if (!empty(self::$plugins[$hook])) {
            foreach (self::$plugins[$hook] as $name) {
                if (!is_array($params)) {
                    @call_user_func(array($name, $hook));
                } else {
                    @call_user_func_array(array($name, $hook), $params);
                }
            }
        }
    }//end call()

    public static function count()
    {
        self::start();
        return count(self::$plugins);
    }
}//end class
