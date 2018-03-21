<?php
/**
 *  Dolumar engine, php/html MMORTS engine
 *  Copyright (C) 2009 Thijs Van der Schaeghe
 *  CatLab Interactive bvba, Gent, Belgium
 *  http://www.catlab.eu/
 *  http://www.dolumar.com/
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License along
 *  with this program; if not, write to the Free Software Foundation, Inc.,
 *  51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 */

abstract class Neuron_Core_ModuleFactory
{
    private $oModules = array();

    /**
     * @param $sModule
     * @return mixed
     * @throws Neuron_Core_Error
     */
    public function __get($sModule)
    {
        $this->doLoadModule($sModule);
        return $this->oModules[$sModule];
    }

    /**
     * @param $sModule
     * @return bool
     * @throws Neuron_Core_Error
     */
    public function moduleExists($sModule)
    {
        $this->doLoadModule($sModule);
        if (!isset ($this->oModules[$sModule])) {
            print_r($this);
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $sModule
     * @throws Neuron_Core_Error
     */
    private function doLoadModule($sModule)
    {
        if (!isset ($this->oModules)) {
            $this->oModules = array();
        }

        if (!isset ($this->oModules[$sModule])) {
            $this->oModules[$sModule] = $this->loadModule($sModule);
        }

        if (!$this->oModules[$sModule]) {
            throw new Neuron_Core_Error('ModuleFactory could not load module ' . $sModule . ' from class ' . get_class($this));
        }
    }

    protected abstract function loadModule($sModule);

    /**
     *
     */
    public function __destruct()
    {
        if (isset ($this->oModules) && is_array($this->oModules)) {
            foreach ($this->oModules as $k => $v) {
                //$v->__destruct ();
                unset ($this->oModules[$k]);
            }
        }

        $this->oModules = array();
    }
}