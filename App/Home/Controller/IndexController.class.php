<?php
/**
 * 网站首页控制器
 * @author lipengqing
 * @create date 2015-11-05
 */
namespace Home\Controller;

/**
* 
*/
class IndexController extends CommonController
{
	
	public function _initialize()
	{

		if (cmf_is_installed()) {
			$this->error('网站已经安装',  '/admin');
		}
		parent::_initialize();
	}

	public function index(){
		
			$this->display('index');
		
	}
 public function step2()
    {
//        if (file_exists_case('data/conf/config.php')) {
//            @unlink('data/conf/config.php');
//        }
        $data               = [];
        $data['phpversion'] = @phpversion();
        $data['os']         = PHP_OS;
        $tmp                = function_exists('gd_info') ? gd_info() : [];
        $server             = $_SERVER["SERVER_SOFTWARE"];
        $host               = (empty($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_HOST"] : $_SERVER["SERVER_ADDR"]);
        $name               = $_SERVER["SERVER_NAME"];
        $max_execution_time = ini_get('max_execution_time');
        $allow_reference    = (ini_get('allow_call_time_pass_reference') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $allow_url_fopen    = (ini_get('allow_url_fopen') ? '<font color=green>[√]On</font>' : '<font color=red>[×]Off</font>');
        $safe_mode          = (ini_get('safe_mode') ? '<font color=red>[×]On</font>' : '<font color=green>[√]Off</font>');

        $err = 0;
        if (empty($tmp['GD Version'])) {
            $gd = '<font color=red>[×]Off</font>';
            $err++;
        } else {
            $gd = '<font color=green>[√]On</font> ' . $tmp['GD Version'];
        }

        if (class_exists('pdo')) {
            $data['pdo'] = '<i class="fa fa-check correct"></i> 已开启';
        } else {
            $data['pdo'] = '<i class="fa fa-remove error"></i> 未开启';
            $err++;
        }

        if (extension_loaded('pdo_mysql')) {
            $data['pdo_mysql'] = '<i class="fa fa-check correct"></i> 已开启';
        } else {
            $data['pdo_mysql'] = '<i class="fa fa-remove error"></i> 未开启';
            $err++;
        }

        if (extension_loaded('curl')) {
            $data['curl'] = '<i class="fa fa-check correct"></i> 已开启';
        } else {
            $data['curl'] = '<i class="fa fa-remove error"></i> 未开启';
            $err++;
        }

        if (extension_loaded('gd')) {
            $data['gd'] = '<i class="fa fa-check correct"></i> 已开启';
        } else {
            $data['gd'] = '<i class="fa fa-remove error"></i> 未开启';
            if (function_exists('imagettftext')) {
                $data['gd'] .= '<br><i class="fa fa-remove error"></i> FreeType Support未开启';
            }
            $err++;
        }

        if (extension_loaded('mbstring')) {
            $data['mbstring'] = '<i class="fa fa-check correct"></i> 已开启';
        } else {
            $data['mbstring'] = '<i class="fa fa-remove error"></i> 未开启';
            if (function_exists('imagettftext')) {
                $data['mbstring'] .= '<br><i class="fa fa-remove error"></i> FreeType Support未开启';
            }
            $err++;
        }

        if (extension_loaded('fileinfo')) {
            $data['fileinfo'] = '<i class="fa fa-check correct"></i> 已开启';
        } else {
            $data['fileinfo'] = '<i class="fa fa-remove error"></i> 未开启';
            $err++;
        }

        if (ini_get('file_uploads')) {
            $data['upload_size'] = '<i class="fa fa-check correct"></i> ' . ini_get('upload_max_filesize');
        } else {
            $data['upload_size'] = '<i class="fa fa-remove error"></i> 禁止上传';
        }

        if (function_exists('session_start')) {
            $data['session'] = '<i class="fa fa-check correct"></i> 支持';
        } else {
            $data['session'] = '<i class="fa fa-remove error"></i> 不支持';
            $err++;
        }

        if (version_compare(phpversion(), '5.6.0', '>=') && version_compare(phpversion(), '7.0.0', '<') && ini_get('always_populate_raw_post_data') != -1) {
            $data['always_populate_raw_post_data']          = '<i class="fa fa-remove error"></i> 未关闭';
            $data['show_always_populate_raw_post_data_tip'] = true;
            $err++;
        } else {

            $data['always_populate_raw_post_data'] = '<i class="fa fa-check correct"></i> 已关闭';
        }

        $folders    = [
            realpath(CMF_ROOT . 'data') . '/',
            realpath('./upload') . '/',
        ];
        $newFolders = [];
        foreach ($folders as $dir) {
            $testDir = $dir;
            sp_dir_create($testDir);
            if (sp_testwrite($testDir)) {
                $newFolders[$dir]['w'] = true;
            } else {
                $newFolders[$dir]['w'] = false;
                $err++;
            }
            if (is_readable($testDir)) {
                $newFolders[$dir]['r'] = true;
            } else {
                $newFolders[$dir]['r'] = false;
                $err++;
            }
        }
        $data['folders'] = $newFolders;

        $this->assign($data);
			$this->display('step2');
    }

    public function step3()
    {
			$this->display('step3');
    }

    public function step4()
    {
        session(null);
        if ($_POST) {
            //创建数据库
            $dbConfig             = [];
            $dbConfig['type']     = "mysql";
            $dbConfig['hostname'] = I('dbhost');
            $dbConfig['username'] = I('dbuser');
            $dbConfig['password'] = I('dbpw');
            $dbConfig['hostport'] = I('dbport');
            $dbConfig['charset']  = I('dbcharset', 'utf8mb4');
            $dbConfig['dbname'] = I('dbname');
            $dbName               = I('dbname');
            $class      =   'Think\\Model';
            $db = new $class;
            $dbs = $db->db(1,"mysql://".$dbConfig['username'].":".$dbConfig['password']."@".$dbConfig['hostname'].":".$dbConfig['hostport']);

            //$this->db(1,"mysql://root:123456@localhost:3306/test")->query("查询SQL");
            
           // $db                   = Db::connect($dbConfig);
            $sql                  = "CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET " . $dbConfig['charset'];

           // var_dump($sql);
            $s = $dbs->query($sql);
            //$db->execute($sql) || $this->error($db->getError());

            $dbConfig['database'] = $dbName;

            //$dbConfig['prefix'] = I('dbprefix', '', 'trim');

            session('installdb_config', $dbConfig);
            //var_dump(APP_PATH . 'Home/data/hdsq.sql');
           // var_dump(file_exists(APP_PATH . 'Home/data/hdsq.sql'));
            $sql = cmf_split_sql(APP_PATH . 'Home/data/hdsq.sql', '', $dbConfig['charset']);
            //var_dump($sql);exit;
            session('installsql', $sql);

            $this->assign('sql_count', count($sql));

            session('installerror', 0);

            $siteName    = I('sitename');
            $seoKeywords = I('sitekeywords');
            $siteInfo    = I('siteinfo');

            session('installsite_info', [
                'site_name'            => $siteName,
                'site_seo_title'       => $siteName,
                'site_seo_keywords'    => $seoKeywords,
                'site_seo_description' => $siteInfo
            ]);

            $userLogin = I('manager');
            $userPass  = I('manager_pwd');
            $userEmail = I('manager_email');

            session('installadmin_info', [
                'user_login' => $userLogin,
                'user_pass'  => $userPass,
                'user_email' => $userEmail
            ]);
            $this->display('step4');

        } else {
            exit;
        }
    }

    public function install()
    {
        $dbConfig = session('installdb_config');
        $sql      = session('installsql');

        if (empty($dbConfig) || empty($sql)) {
            $this->error("非法安装!");
        }

        $sqlIndex = I('sql_index', 0, 'intval');

        $class      =   'Think\\Model';
        $db = new $class;
        $dbs = $db->db(1,"mysql://".$dbConfig['username'].":".$dbConfig['password']."@".$dbConfig['hostname'].":".$dbConfig['hostport'].'/'.$dbConfig['dbname']);
        

        if ($sqlIndex >= count($sql)) {
            $installError = session('installerror');
            $this->success("安装完成!", '', ['done' => 1, 'error' => $installError]);
        }

        $sqlToExec = $sql[$sqlIndex] . ';';

        $result = sp_execute_sql($dbs, $sqlToExec);

        if (!empty($result['error'])) {
            $installError = session('installerror');
            $installError = empty($installError) ? 0 : $installError;

            session('installerror', $installError + 1);
            $this->error($result['message'], '', [
                'sql'       => $sqlToExec,
                'exception' => $result['exception']
            ]);
        } else {
            $this->success($result['message'], '', [
                'sql' => $sqlToExec
            ]);
        }

    }

    public function setDbConfig()
    {
        $dbConfig = session('installdb_config');

        $dbConfig['authcode'] = cmf_random_string(18);

        $result = sp_create_db_config($dbConfig);

        if ($result) {
            $this->success("数据配置文件写入成功!");
        } else {
            $this->error("数据配置文件写入失败!");
        }
    }

    public function setSite()
    {
        $dbConfig = session('installdb_config');

        if (empty($dbConfig)) {
            $this->error("非法安装!");
        }

        $siteInfo               = session('installsite_info');
        $admin                  = session('installadmin_info');
        $admins['username']     = $admin['username'];
        $admins['userpass']     = MD5($admin['user_pass']);
        $admins['time']   = time();
		$web['NAME'] = $siteInfo['site_name'];
		$web['DOMAIN'] = $_SERVER['HTTP_HOST'];
		$web['SEONAME'] = $siteInfo['site_name'];
		$web['SEOKEYWORD'] = $siteInfo['site_seo_keywords'];
		$web['SEODESCRIPTION'] = $siteInfo['site_seo_description'];

		$res = M('admin')->add($admins);
        try {
            $res = M('webset')->where('id=1')->save($web);
        } catch (\Exception $e) {
            $this->error("网站创建失败!");
        }

        $this->success("网站创建完成!");

    }

    public function installTheme()
    {
        session("installstep", 4);
        $this->success("模板安装成功");
        $themeModel = new ThemeModel();
        $result     = $themeModel->installTheme(config('cmf_default_theme'));
        if ($result === false) {
            $this->error('模板不存在!');
        }

        $this->success("模板安装成功");
    }

    public function step5()
    {
        if (session("installstep") == 4) {
            @touch(CMF_ROOT . 'data/install.lock');
            //return $this->fetch(":step5");
            $this->display('step5');
        } else {
            $this->error("非法安装！");
        }
    }

    public function testDbPwd()
    {
        if ($this->request->isPost()) {
            $dbConfig         = I();
            $dbConfig['type'] = "mysql";

            try {
                Db::connect($dbConfig)->query("SELECT VERSION();");
            } catch (\Exception $e) {
                die("");
            }
            exit("1");
        } else {
            exit("need post!");
        }

    }
    

}