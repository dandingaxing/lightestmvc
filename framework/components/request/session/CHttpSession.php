<?php
/**
 * CHttpSession class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CHttpSession provides session-level data management and the related configurations.
 *
 * To start the session, call {@link open()}; To complete and send out session data, call {@link close()};
 * To destroy the session, call {@link destroy()}.
 *
 * If {@link autoStart} is set true, the session will be started automatically
 * when the application component is initialized by the application.
 *
 * CHttpSession can be used like an array to set and get session data. For example,
 * <pre>
 *   $session=new CHttpSession;
 *   $session->open();
 *   $value1=$session['name1'];  // get session variable 'name1'
 *   $value2=$session['name2'];  // get session variable 'name2'
 *   foreach($session as $name=>$value) // traverse all session variables
 *   $session['name3']=$value3;  // set session variable 'name3'
 * </pre>
 *
 * The following configurations are available for session:
 * <ul>
 * <li>{@link setSessionID sessionID};</li>
 * <li>{@link setSessionName sessionName};</li>
 * <li>{@link autoStart};</li>
 * <li>{@link setSavePath savePath};</li>
 * <li>{@link setCookieParams cookieParams};</li>
 * <li>{@link setGCProbability gcProbability};</li>
 * <li>{@link setCookieMode cookieMode};</li>
 * <li>{@link setUseTransparentSessionID useTransparentSessionID};</li>
 * <li>{@link setTimeout timeout}.</li>
 * </ul>
 * See the corresponding setter and getter documentation for more information.
 * Note, these properties must be set before the session is started.
 *
 * CHttpSession can be extended to support customized session storage.
 * Override {@link openSession}, {@link closeSession}, {@link readSession},
 * {@link writeSession}, {@link destroySession} and {@link gcSession}
 * and set {@link useCustomStorage} to true.
 * Then, the session data will be stored and retrieved using the above methods.
 *
 * CHttpSession is a Web application component that can be accessed via
 * {@link CWebApplication::getSession()}.
 *
 * @property boolean $useCustomStorage Whether to use custom storage.
 * @property boolean $isStarted Whether the session has started.
 * @property string $sessionID The current session ID.
 * @property string $sessionName The current session name.
 * @property string $savePath The current session save path, defaults to {@link http://php.net/session.save_path}.
 * @property array $cookieParams The session cookie parameters.
 * @property string $cookieMode How to use cookie to store session ID. Defaults to 'Allow'.
 * @property float $gCProbability The probability (percentage) that the gc (garbage collection) process is started on every session initialization, defaults to 1 meaning 1% chance.
 * @property boolean $useTransparentSessionID Whether transparent sid support is enabled or not, defaults to false.
 * @property integer $timeout The number of seconds after which data will be seen as 'garbage' and cleaned up, defaults to 1440 seconds.
 * @property CHttpSessionIterator $iterator An iterator for traversing the session variables.
 * @property integer $count The number of session variables.
 * @property array $keys The list of session variable names.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package system.web
 * @since 1.0
 */
class CHttpSession
{
    /**
     * @var boolean whether the session should be automatically started when the session application component is initialized, defaults to true.
     */
    public $autoStart=true;

    public function __construct(){
        $this->init();
    }

    /**
     * Initializes the application component.
     * This method is required by IApplicationComponent and is invoked by application.
     */
    public function init()
    {
        parent::init();

        if($this->autoStart)
            $this->open();
        register_shutdown_function(array($this,'close'));
    }

    /**
     * Starts the session if it has not started yet.
     */
    public function open()
    {
        @session_start();
        if(session_id()=='')
        {
            return false;
        }
    }

    /**
     * Ends the current session and store session data.
     */
    public function close()
    {
        if(session_id()!=='')
            @session_write_close();
    }

    /**
     * Adds a session variable.
     * Note, if the specified name already exists, the old value will be removed first.
     * @param mixed $key session variable name
     * @param mixed $value session variable value
     */
    public function set($key,$value)
    {
        $_SESSION[$key]=$value;
    }

    /**
     * [remove 删除一个session]
     * @param  [type]  $key    [键名]
     * @param  boolean $return [是否返回]
     * @return [type]          [description]
     */
    public function remove($key, $return=true)
    {
        if(isset($_SESSION[$key]))
        {
            if ($return) {
                $value=$_SESSION[$key];
                unset($_SESSION[$key]);
                return $value;
            }else{
                return unset($_SESSION[$key]);
            }
        }
        else
            return null;
    }

    /**
     * Removes all session variables
     */
    public function clearAll()
    {
        foreach(array_keys($_SESSION) as $key)
            unset($_SESSION[$key]);
    }

    /**
     * Frees all session variables and destroys all data registered to a session.
     */
    public function destroy()
    {
        if(session_id()!=='')
        {
            @session_unset();
            @session_destroy();
        }
    }

    /**
     * @return boolean whether the session has started
     */
    public function getIsStarted()
    {
        return session_id()!=='';
    }

    /**
     * @return string the current session ID
     */
    public function getSessionID()
    {
        return session_id();
    }

    /**
     * @param string $value the session ID for the current session
     */
    public function setSessionID($value)
    {
        session_id($value);
    }

    /**
     * Updates the current session id with a newly generated one .
     * Please refer to {@link http://php.net/session_regenerate_id} for more details.
     * @param boolean $deleteOldSession Whether to delete the old associated session file or not.
     * @since 1.1.8
     */
    public function regenerateID($deleteOldSession=false)
    {
        session_regenerate_id($deleteOldSession);
    }

    /**
     * @return string the current session name
     */
    public function getSessionName()
    {
        return session_name();
    }

    /**
     * @param string $value the session name for the current session, must be an alphanumeric string, defaults to PHPSESSID
     */
    public function setSessionName($value)
    {
        session_name($value);
    }

    /**
     * @return string the current session save path, defaults to {@link http://php.net/session.save_path}.
     */
    public function getSavePath()
    {
        return session_save_path();
    }

    /**
     * @param string $value the current session save path
     * @throws CException if the path is not a valid directory
     */
    public function setSavePath($value)
    {
        if(is_dir($value))
            session_save_path($value);
        else{
            return false;
        }
    }

    /**
     * @return array the session cookie parameters.
     * @see http://us2.php.net/manual/en/function.session-get-cookie-params.php
     */
    public function getCookieParams()
    {
        return session_get_cookie_params();
    }

    /**
     * Sets the session cookie parameters.
     * The effect of this method only lasts for the duration of the script.
     * Call this method before the session starts.
     * @param array $value cookie parameters, valid keys include: lifetime, path,
     * domain, secure, httponly. Note that httponly is all lowercase.
     * @see http://us2.php.net/manual/en/function.session-set-cookie-params.php
     */
    public function setCookieParams($lifetime, $path='/', $domain='', $secure=false, $httponly=false)
    {
        session_set_cookie_params($lifetime,$path,$domain,$secure,$httponly);
    }

    /**
     * @return string how to use cookie to store session ID. Defaults to 'Allow'.
     */
    public function getCookieMode()
    {
        if(ini_get('session.use_cookies')==='0')
            return 'none';
        elseif(ini_get('session.use_only_cookies')==='0')
            return 'allow';
        else
            return 'only';
    }

    /**
     * @param string $value how to use cookie to store session ID. Valid values include 'none', 'allow' and 'only'.
     */
    public function setCookieMode($value)
    {
        if($value==='none')
        {
            ini_set('session.use_cookies','0');
            ini_set('session.use_only_cookies','0');
        }
        elseif($value==='allow')
        {
            ini_set('session.use_cookies','1');
            ini_set('session.use_only_cookies','0');
        }
        elseif($value==='only')
        {
            ini_set('session.use_cookies','1');
            ini_set('session.use_only_cookies','1');
        }
        else{
            return false;
        }
    }

    /**
     * @return float the probability (percentage) that the gc (garbage collection) process is started on every session initialization, defaults to 1 meaning 1% chance.
     */
    public function getGCProbability()
    {
        return (float)(ini_get('session.gc_probability')/ini_get('session.gc_divisor')*100);
    }

    /**
     * @param float $value the probability (percentage) that the gc (garbage collection) process is started on every session initialization.
     * @throws CException if the value is beyond [0,100]
     */
    public function setGCProbability($value)
    {
        if($value>=0 && $value<=100)
        {
            // percent * 21474837 / 2147483647 ≈ percent * 0.01
            ini_set('session.gc_probability',floor($value*21474836.47));
            ini_set('session.gc_divisor',2147483647);
        }else{
            return false;
        }
    }

    /**
     * @return boolean whether transparent sid support is enabled or not, defaults to false.
     */
    public function getUseTransparentSessionID()
    {
        return ini_get('session.use_trans_sid')==1;
    }

    /**
     * @param boolean $value whether transparent sid support is enabled or not.
     */
    public function setUseTransparentSessionID($value)
    {
        ini_set('session.use_trans_sid',$value?'1':'0');
    }

    /**
     * @return integer the number of seconds after which data will be seen as 'garbage' and cleaned up, defaults to 1440 seconds.
     */
    public function getTimeout()
    {
        return (int)ini_get('session.gc_maxlifetime');
    }

    /**
     * @param integer $value the number of seconds after which data will be seen as 'garbage' and cleaned up
     */
    public function setTimeout($value)
    {
        ini_set('session.gc_maxlifetime',$value);
    }

    /**
     * Returns the number of items in the session.
     * @return integer the number of session variables
     */
    public function getCount()
    {
        return count($_SESSION);
    }

    /**
     * Returns the number of items in the session.
     * This method is required by Countable interface.
     * @return integer number of items in the session.
     */
    public function count()
    {
        return $this->getCount();
    }

    /**
     * @return array the list of session variable names
     */
    public function getKeys()
    {
        return array_keys($_SESSION);
    }

    /**
     * Returns the session variable value with the session variable name.
     * This method is very similar to {@link itemAt} and {@link offsetGet},
     * except that it will return $defaultValue if the session variable does not exist.
     * @param mixed $key the session variable name
     * @param mixed $defaultValue the default value to be returned when the session variable does not exist.
     * @return mixed the session variable value, or $defaultValue if the session variable does not exist.
     * @since 1.1.2
     */
    public function get($key,$defaultValue=null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }



    /**
     * @param mixed $key session variable name
     * @return boolean whether there is the named session variable
     */
    public function contains($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * @return array the list of all session variables in array
     */
    public function toArray()
    {
        return $_SESSION;
    }

}
