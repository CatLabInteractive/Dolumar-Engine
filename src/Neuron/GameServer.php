<?php
class Neuron_GameServer
{
	private $objGame;
	private $objMap;
	private $dispatch_url = ABSOLUTE_URL;
	
	const SCRIPT_PATH = 'Neuron/GameServer/scripts/';
	
	// Initialization
	protected final function __construct ()
	{
		Neuron_Core_Template::load ();
		add_to_template_path (CATLAB_BASEPATH . 'templates');
	}
	
	/*
		This method let's you set the URL that will
		be contacted with the ajax requests. The API call
		will be appended to this URL.
		
		For example, if you set it to index.php?action=, the
		action will be set after the =.
	*/
	public function setDispatchURL ($url)
	{
		$this->dispatch_url = $url;
	}
	
	public function getDispatchURL ()
	{
		if (isset ($this->dispatch_url))
		{
			return $this->dispatch_url;
		}
		
		else
		{
			return ABSOLUTE_URL;
		}
	}
	
	public function setGame (Neuron_GameServer_Interfaces_Game $objGame)
	{
		/*
		if (!$objGame instanceof Neuron_GameServer_Interfaces_Game)
		{
			throw new Neuron_Core_Error ('objGame does not implement Neuron_GameServer_Game.');
		}
		*/
		$this->objGame = $objGame;
	}
	
	public function getGame ()
	{
		if (!isset ($this->objGame) || !($this->objGame instanceof Neuron_GameServer_Interfaces_Game))
		{
			throw new Neuron_Core_Error ('You should call setGame before dispatching the game server request.');
		}
		return $this->objGame;
	}
	
	public function setMap ($objMap)
	{
		if (!$objMap instanceof Neuron_GameServer_Map_Map)
		{
			throw new Neuron_Core_Error ('map does not implement Neuron_GameServer_Map_Map.');
		}
		$this->objMap = $objMap;
	}
	
	public function getMap ()
	{
		if (!isset ($this->objMap))
		{
			$map = $this->objGame->getMap ();
			if ($map)
			{
				$this->setMap ($map);
			}
			else
			{
				$this->objMap = false;
			}
		}
		
		return $this->objMap;
	}
	
	public static function getInstance ()
	{
		static $in;

		if (!isset ($in))
		{
			$in = new self ();
		}
		return $in;
	}
	
	/*
		Return a (checked) player object
		@param id: If null, return the currently logged in user.
		@param data: MAY contain the user data loaded by the loadData function.
	*/
	public static function getPlayer ($id = null, $data = null)
	{		
		static $in;
		
		if (!isset ($id))
		{
			$login = Neuron_Core_Login::getInstance ();
			if ($login->isLogin ())
			{
				$id = $login->getUserId ();
			}
			else
			{
				return false;
			}
		}
		
		if (!defined ('DISABLE_STATIC_FACTORY'))
		{
			if (!isset ($in[$id]))
			{
				$in[$id] = self::getFreshPlayer ($id, $data);
			}
			return $in[$id];
		}
		else
		{
			return self::getFreshPlayer ($id, $data);
		}
	}
	
	public static function getServer ()
	{
		static $in;
		
		if (!isset ($in))
		{
			$server = self::getInstance ();
			$game = $server->getGame ();
			$in = $game->getServer ();

			if (! ($in instanceof Neuron_GameServer_Server))
			{
				throw new Neuron_Exceptions_InvalidParameter ("Server should implement Neuron_GameServer_Server");
			}
		}
		
		return $in;
	}
	
	public static function getPlayerFromOpenID ($openid, $isHashed = false)
	{
		$db = Neuron_DB_Database::getInstance ();
		
		$user = $db->query
		("
			SELECT
				user_id
			FROM
				n_auth_openid
			WHERE
				MD5(openid_url) = '{$db->escape($openid)}'
		");
		
		if (count ($user) == 1)
		{
			return self::getPlayer ($user[0]['user_id']);
		}
		else
		{
			return false;
		}
	}
	
	/*
		A help function for getPlayer.
	*/
	private static function getFreshPlayer ($id, $data)
	{
		$server = self::getInstance ();
		
		$player = $server->objGame->getPlayer ($id);
		if ($player instanceof Neuron_GameServer_Player)
		{
			try
			{
				if (!empty ($data))
				{
					$player->setData ($data);
				}
			}
			
			catch (Exception $e)
			{
			
				echo $e;
			}
			
			$player->init ();
			
			return $player;
		}
		elseif ($player === false)
		{
			return false;
		}
		else
		{
			throw new Neuron_Core_Error ('Object returned from Game::getPlayer does not extend Neuron_GameServer_Player.');
		}
	}

	public function openid ()
	{
		$openid = new Neuron_Auth_OpenID ();
		$openid->dispatch ();
	}

	/*
		Get the output that is required.
	*/
	public function dispatch ()
	{		
		if (!isset ($this->objGame))
		{
			throw new Neuron_Core_Error ('Neuron_GameServer did not receive a Neuron_GameServer_Game object.');
		}

		if ($this->objGame instanceof Neuron_GameServer_Interfaces_Dispatch)
		{
			if (!$this->getRidOfSessionID ())
			{
				return;
			}

			$this->objGame->dispatch ();
			return;
		}

		Neuron_URLBuilder::getInstance ()->setOpenCallback (array ($this, 'getOpenUrl'));
		Neuron_URLBuilder::getInstance ()->setUpdateCallback (array ($this, 'getUpdateUrl'));
	
		$pgen = Neuron_Core_PGen::__getInstance ();
		$pgen->start ();

		// Start counter
		$sInputs = explode ('/', isset ($_GET['module']) ? $_GET['module'] : null);
		$sModule = $sInputs[0];

		switch ($sModule)
		{
			case 'openid':
				$this->openid ();
			break;

			case 'dialog':

				// A little overwrite 
				$output = $this->objGame->getCustomOutput ();
				if ($output)
				{
					header("Content-Type: text/xml; charset=utf-8;");
					
					echo '<?xml version="1.0" encoding="utf-8"?>';
					echo '<root><command command="refresh"></command></root>';
					return;	
				}

				require_once (self::SCRIPT_PATH.'dialog/dialog.php');
			break;
	
			case 'api':
				require_once (self::SCRIPT_PATH.'api/api.php');
			break;
	
			case 'map':
				// Close the session (lock)
				//session_write_close ();
	
				require_once (self::SCRIPT_PATH.'map/map.php');
			break;
	
			case 'image':
				// Close the session (lock)
				session_write_close ();
		
				require_once (self::SCRIPT_PATH.'image/image.php');
			break;
	
			case 'test':
				// Close the session (lock)
				session_write_close ();
				
				// Login
				$player = Neuron_GameServer::getPlayer ();
				
				$overwritelogin = true;
				
				if ((!$player || !$player->isAdmin ()) && !$overwritelogin)
				{
					echo 'You must login.';
				}
				else
				{
					$file = isset ($sInputs[1]) ? $sInputs[1] : null;

					if (@include ('scripts/tests/' . $sInputs[1] . '.php'))
					{
						//include_once ('scripts/tests/' . $sInputs[1] . '.php');
					}
					else if (include (self::SCRIPT_PATH.'tests/'.$file.'.php'))
					{
						// ok	
					}
					else
					{
						echo "File not found: " . self::SCRIPT_PATH.'tests/'.$file.'.php';
					}

					/*if (file_exists (self::SCRIPT_PATH.'tests/'.$file.'.php'))
					{
					*/

					/*}
					else
					{
						echo "File not found: " . self::SCRIPT_PATH.'tests/'.$file.'.php';
					}*/
				}
			break;
	
			case 'admin':
				// Check for page
				setcookie ('session_id', session_id (), 0, COOKIE_BASE_PATH.'admin/');		
				
				$login = Neuron_Core_Login::getInstance (1);
				if (!$login->isLogin ())
				{
					$objPage = new Neuron_GameServer_Pages_Admin_Login ($login);
				}
				else
				{
					$sPage = isset ($sInputs[1]) ? $sInputs[1] : 'Index';
					$sClassname = 'Neuron_GameServer_Pages_Admin_'.ucfirst (strtolower ($sPage));
	
					$myself = Neuron_GameServer::getPlayer ();
	
					if ($myself && $myself->isChatModerator ())
					{
						if ($objPage = $this->objGame->getAdminPage ($sPage))
						{
						}				
				
						elseif (class_exists ($sClassname))
						{
							$objPage = new $sClassname ();
						}
						else
						{
							$objPage = new Neuron_GameServer_Pages_Admin_Index ();
						}
					}
					else
					{
						$objPage = new Neuron_GameServer_Pages_Admin_Invalid ();
					}
				}
	
				echo $objPage->getHTML ();
			break;
			
			case 'page':
				// Check for page
				$sPage = isset ($sInputs[1]) ? $sInputs[1] : 'Index';
				$sClassname = 'Neuron_GameServer_Pages_'.ucfirst (strtolower ($sPage));
		
				$myself = Neuron_GameServer::getPlayer ();

				if ($objPage = $this->objGame->getPage ($sPage))
				{
				}
		
				else if (class_exists ($sClassname))
				{
					$objPage = new $sClassname ();
				}
				else
				{
					$objPage = new Neuron_GameServer_Pages_Index ();
				}
		
				echo $objPage->getOutput ();
			break;
			
			case 'time':
				echo 'time=' . round (microtime (true) * 1000);
			break;
			
			case '':
				$_SESSION['tmp'] = null;

				// Now, if we have a NOLOGIN_REDIRECT set, redirect here
				if (defined ('NOLOGIN_REDIRECT') && !isset ($_GET['DEBUG']))
				{
					$player = Neuron_GameServer::getPlayer ();

					if (!$player)
					{
						header ("Location: " . NOLOGIN_REDIRECT);
						echo "Redirecting to " . NOLOGIN_REDIRECT;
					}

					else
					{
						$this->showIndexPage ();
					}
				}

				else
				{
					$this->showIndexPage ();
				}
			break;

			case 'favicon.ico':
			case 'favicon.icon':
				header ('Content-type: image/x-icon');
				echo file_get_contents ('./favicon.ico');
			break;
	
			default:
				//throw new Exception ('Invalid API call: module "'.$sModule.'" not found.');
				echo '<p>Invalid module: '.$sModule.'</p>';
			break;
		}

		if (isset ($profiler) && defined (USE_PROFILE) && USE_PROFILE)
		{
			// Dump the profiler
			if (intval($profiler->getTotalDuration ()) > 2)
			{
				$cache = Neuron_Core_Cache::__getInstance ('profiler/'.$_SERVER['REMOTE_ADDR'].'/');
				$cache->setCache (date ('dmYHis'), (string)$profiler);
			}
		}
	}

	/*
		This method is used for the dialog method.
		It returns all open windows (as received by post[updatewindow])
		ordered by their priority (so basically all those wiht input go first)
	*/
	private function getOpenWindows ()
	{
		$profiler = Neuron_Profiler_Profiler::getInstance ();

		$out = array ();

		// List all open windows
		$updatewindows = isset ($_POST['updatewindow']) ? $_POST['updatewindow'] : null;
		if (is_array ($updatewindows))
		{
			$profiler->start ('Ordering windows on priority');

			// Order window on input (put input first)
			$aantal = count ($updatewindows);
			for ($i = 0; $i < $aantal; $i ++)
			{
				$vervangen = $i;
				
				// Loop trough last, fine the one with more input.
				for ($j = $i + 1; $j < $aantal; $j ++)
				{
					if 
					(
						isset  ($updatewindows[$j]['sInputData']) && 
						strlen ($updatewindows[$j]['sInputData']) > strlen ($updatewindows[$vervangen]['sInputData'])
					)
					{
						$vervangen = $j;
					}
				}
				
				// Vervangen
				$temp = $updatewindows[$i];
				$updatewindows[$i] = $updatewindows[$vervangen];
				$updatewindows[$vervangen] = $temp;
			}
			
			$profiler->stop ();

			//foreach ($updatewindows as $v)
			for ($i = 0; $i < count ($updatewindows); $i ++)
			{
				$v = $updatewindows[$i];
			
				if (is_array ($v) && count ($v) == 4)
				{
					// Put everything in a big TRY
					$profiler->start ('Loading window '.$v['sWindowId']);
					
					try
					{
						$window = $this->getWindow ($v['sWindowId']);
					
						if ($window)
						{
							$window->setDivId ($v['sDialogId']);
						
							// Set request data
							if (isset ($v['sRequestData']))
							{
								$window->setRequestData ($v['sRequestData']);
							}
						
							// Set input data
							$window->setJsonInputData ($v['sInputData']);
						
							// Initialize
							$window->setSettings ();
						}

						$out[] = $window;
					}
					catch (Exception $e)
					{
						// Send a mail
						error_log ((string)$e);
						
						if (defined ('OUTPUT_DEBUG_DATA') && OUTPUT_DEBUG_DATA)
						{
							echo $e;
						}
					}
					
					$profiler->stop ();
				}
			}
		}

		return $out;
	}

	private function getRidOfSessionID ()
	{
		if (isset ($_GET['session_id']))
		{
			if (isset ($_COOKIE['session_id']))
			{

				$module = isset ($_GET['module']) ? $_GET['module'] : null;

				// All is okay now
				$url = ABSOLUTE_URL . $module . '?';

				unset ($_GET['module']);
				unset ($_GET['session_pass']);

				foreach ($_GET as $k => $v)
				{
					if ($k != 'session_id')
					{
						$url .= $k . '=' . $v . '&';
					}
				}
				$url = substr ($url, 0, -1);

				header ("Location: " . $url);
				echo '<p>Redirecting to <a href="' . $url . '">' . $url . '</a>.';
				
				return false;
			}

			else if (isset ($_GET['session_pass']))
			{
				// Seems like cookies aren't permitted. Do nothing
				return true;
			}

			else
			{
				setcookie ('session_id', $_GET['session_id']);
				$url = ABSOLUTE_URL . '?session_id=' . $_GET['session_id'] . '&session_pass=1';

				header ("Location: " . $url);
				echo '<p>Redirecting to <a href="' . $url . '">' . $url . '</a>.';	
				
				return false;
			}
		}

		return true;
	}
	
	private function showIndexPage ()
	{
		// A little overwrite 
		$output = $this->objGame->getCustomOutput ();
		if ($output)
		{
			echo $output;
			exit;
		}

		if (!$this->getRidOfSessionID ())
		{
			return;
		}

		// Check for cache dir
		if (!file_exists (CACHE_DIR) || !is_writable (CACHE_DIR))
		{
			echo CACHE_DIR . ' should be writeable';
			exit;
		}

		// Cache for memcache
		if (!class_exists ('Memcache'))
		{
			echo 'Memcache php module must be installed.';
			exit;
		}

		$page = new Neuron_Core_Template ();
		
		$page->set ('server', $this);
		
		$page->set ('static_client_url', BASE_URL . 'gameserver/');
		
		if (defined ('IS_TESTSERVER') && IS_TESTSERVER)
		{
			$page->set ('application_version', time ());
		}
		else
		{
			$page->set ('application_version', APP_VERSION);
		}
		
		// Plugins
		$header = '';
		if (Neuron_Core_Template::hasTemplate ('gameserver/index/header.phpt'))
			$header = $page->parse ('gameserver/index/header.phpt');
			
		$body = '';
		if (Neuron_Core_Template::hasTemplate ('gameserver/index/body.phpt'))
			$body = $page->parse ('gameserver/index/body.phpt');
			
		$jssettings = '';
		if (Neuron_Core_Template::hasTemplate ('gameserver/index/jssettings.phpt'))
			$jssettings = $page->parse ('gameserver/index/jssettings.phpt');
		
		$page->set ('header', $header);
		$page->set ('body', $body);
		$page->set ('jssettings', $jssettings);
		
		$page->set ('dispatch_url', $this->getDispatchURL ());
		
		$size = $this->getMap ()->getBackgroundManager ()->getTileSize ();
		$page->set ('map_tile_size', json_encode ($size));

		$premium = false;
	
		// Load start location
		if (!isset ($_GET['x']) || !isset ($_GET['y']))
		{
			$location = $this->getInitialLocation ();
			
			if (isset ($location))
			{
				$_GET['x'] = $location[0];
				$_GET['y'] = $location[1];			
			}
		}
		
		// IS premium
		$player = Neuron_GameServer::getPlayer ();
		if ($player)
		{
			$page->set ('premium', $player->isPremium ());
		}
		else
		{
			$page->set ('premium', false);
		}
		
		echo $page->parse ('gameserver/index.phpt');
	}
	
	private function getInitialLocation ()
	{
		$location = $this->getGame ()->getMap ()->getInitialLocation ();
		
		if (isset ($location))
		{
			return $location;
		}
		return null;
	}
	
	public function getOpenUrl ($module, $display, $data, $title = null, $url = null)
	{
		if ($url === null)
		{
			$url = 'javascript:void(0);';
		}

		$json = json_encode ($data);
		$json = str_replace ('"', "'", $json);
		return '<a href="'.$url.'" onclick="openWindow(\''.$module.'\','.$json.'); return false;" title="'.$title.'">'.$display.'</a>';
	}
	
	public function getUpdateUrl ($module, $display, $data, $title = null)
	{
		$json = json_encode ($data);
		$json = str_replace ('"', "'", $json);
		return '<a href="javascript:void(0);" onclick="windowAction(this,'.$json.');" title="'.$title.'">'.$display.'</a>';
	}
	
	/*
		Return the right window object
	*/
	public function getWindow ($sWindow)
	{
		$objWindow = $this->objGame->getWindow ($sWindow);
		if ($objWindow instanceof Neuron_GameServer_Interfaces_Window)
		{
			$window = $objWindow;
		}
		else
		{
			$sClassName = 'Neuron_GameServer_Windows_'.ucfirst ($sWindow);
			if (class_exists ($sClassName))
			{
				$window = new $sClassName ();
			}
			else
			{
				throw new Neuron_Core_Error ('Window not found: '.$sWindow); 
			}
		}
		
		$window->setServer ($this);
		
		return $window;
	}
	
	public function openWindow ($sWindow, $aParams)
	{
		$window = $this->getWindow ($sWindow);
		$window->setRequestData (json_encode ($aParams));

		// Check if unique (in this request)
		$windows = $this->getOpenWindows ();
		foreach ($windows as $v)
		{
			if ($v->equals ($window))
			{
				return;
			}
		}
	
		handleOpenWindowRequest (array ($window));
	}	
	
	/*
		Get initial windows
	*/
	public function getInitialWindows ()
	{
		$windows = $this->objGame->getInitialWindows ($this);
		
		// The map updater will make sure the map is up to date.
		$windows[] = $this->getWindow ('MapUpdater');
		
		return $windows;
	}
	
	/*
		Map logs
	*/
	public static function addMapUpdate ($x, $y, $action)
	{
		switch ($action)
		{
			case 'BUILD':
			case 'DESTROY':
				
			break;
			
			default:
				$action = 'BUILD';
			break;
		}
		
		$db = Neuron_DB_Database::getInstance ();
		
		$x = intval ($x);
		$y = intval ($y);
		
		$db->query
		("
			INSERT INTO
				n_map_updates
			SET
				mu_action = '{$action}',
				mu_x = {$x},
				mu_y = {$y},
				mu_date = FROM_UNIXTIME(".NOW.")
		");
	}
}