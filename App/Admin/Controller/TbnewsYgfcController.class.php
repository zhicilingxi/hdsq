<?php
namespace Admin\Controller;
/*
*新闻内容管理控制器，继承CommonController
*方法：
*       
*作者：李彭青 
*/
class TbnewsYgfcController extends CommonController {

    private $cityarea;
    private $citylevel;
    public function __construct(){
		
        parent::__construct();
        $this->cityarea = M('Cityarea');
        $this->citylevel = $_SESSION['scadminuser']['cid'];

        $newsclass = M('tbnewsclass')->where("ClassName='员工风采'")->field('ClassID,ClassName')->select();
        $this->assign('newsclass',$newsclass);
        $city = M('city')->where('id in ('.$this->citylevel.')')->select();
        $this->assign('city',$city);
    }

    /*
     *封装搜索条件，自动调用的方法
     */
    public function _filter(&$map){
        
        // 如果小区名称name有值则进行封装搜索
        if(!empty($_REQUEST['keyword']) ){
            $where['Title']  = array('like', "%{$_REQUEST['keyword']}%");
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
        }
        
        // 只允许显示拥有城市权限的小区
        $map['CityID'] = array('in',$this->citylevel);
        $map['ClassID'] = 21;

        // 如果城市名称(CID)有值则进行封装搜索
        if(!empty($_REQUEST['CityID'])){
           $map['CityID'] = array('in',$_REQUEST['CityID']);
        }
       
    }

    /*加载新闻内容页面
     */
    public function index(){

        // 此方法的权限ID为53
        $this->checkLevel(53);
    	$model = M('tbnews');
        $map = $this->_search();
        if(method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        
        if(!empty($model)) {
            $this->_list($model, $map);
        }
        $this->display();
        return;
    }
    
    /*使用 CommonController回调函数_tigger_list，用于数据加工
     */
    public function _tigger_list(&$list){

        // 将list数据遍历后作相应处理
        foreach($list as &$v){

            // 将数据库中datetime的时间转化为时间戳
            $v['updatetime'] = strtotime($v['UpdateTime']);

            // 处理输出是否在首页
            switch($v['InIndex']){
                case 0:
                    $v['inindex'] = '否';
                    break;
                case 1:
                    $v['inindex'] = '是';
                    break;
            }

            // 处理输出是否在首页
            switch($v['Hot']){
                case 0:
                    $v['hot'] = '否';
                    break;
                case 1:
                    $v['hot'] = '是';
                    break;
            }

            // 处理输出是否在首页
            switch($v['Statusdescription']){
                case 0:
                    $v['status'] = '待审核';
                    break;
                case 1:
                    $v['status'] = '审核通过';
                    break;
                case -1:
                    $v['status'] = '审核不通过';
                    break;
            }

        }
        return $list;
    }

    /*加载新闻内容添加页面
     */
    public function add(){

        // 此方法的权限ID为54
        $this->checkLevel(54);
        define(CONTROLLER_NAME, 'tbnews');
        parent::add();
    }

    /*
     *加载上传图片对话框页面
     */
    public function upindex(){
        $this->display();
    }

    /*执行新闻内容添加后的数据写入
     */
    public function insert(){
        // 此方法的权限ID为54
        $this->checkLevel(54);
    	$model = M('tbnews');
        // 默认等级为0
        $_POST['Stars'] = '0';
        // 审核状态为0待审核
        $_POST['Statusdescription'] = '0';
        // 管理员为当前登录用户
        $_POST['Editor'] = $_SESSION['scadminuser']['username'];
        $_POST['ClassName'] = '员工风采';
        unset( $_POST[$model->getPk()]);
        
        if (false === $model->create()) {
            $this->error($model->getError());
        }
        //保存当前数据对象
        if ($result = $model->add()) { //保存成功
            // 回调接口
            if (method_exists($this, '_tigger_insert')) {
                $model->id = $result;
                $this->_tigger_insert($model);
            }
            
            //成功提示
            $this->success(L('新增成功'));
        } else {
            //失败提示
            $this->error(L('新增失败').$model->getLastSql());
        }
    }

    /*加载新闻内容编辑页面
     */
    public function edit(){

        // 此方法的权限ID为55
        $this->checkLevel(55);
    	$model = M('tbnews');
        $id = $_REQUEST[$model->getPk()];
        $vo = $model->find($id);
        $this->assign('vo', $vo);
        $this->display('edit');
    }

    /*执行新闻内容编辑后的数据更新
     */
    public function update(){

        // 此方法的权限ID为55
        $this->checkLevel(55);
    	$model = M('tbnews');
        if(empty($_POST['DefaultPicUrl'])){
            unset($_POST['DefaultPicUrl']);
        }
        $_POST['Editor'] = $_SESSION['scadminuser']['username'];

        $_POST['ClassName'] = '员工风采';
        if(false === $model->create()) {
            $this->error($model->getError());
        }
        // 更新数据
        if(false !== $model->save()) {
            // 回调接口
            if (method_exists($this, '_tigger_update')) {
                $this->_tigger_update($model);
            }
            //成功提示
            $this->success(L('更新成功'));
        } else {
            //错误提示
            $this->error(L('更新失败'));
        }
    }

    /*加载新闻内容审核页面
     */
    public function check(){

        // 此方法的权限ID为56
        $this->checkLevel(56);
        $id = I('get.ID',0,'intval');
        $newsstatus = M('tbnews')->where('ID='.$id)->field('ID,Statusdescription')->find();
        $this->assign('newsstatus',$newsstatus);
        $this->display("check");
    }

    public function audit(){
        $this->checkLevel(56);
        (!$_REQUEST['ids'] || !$_GET['type']) && $this->error(L('缺少必要的参数！'));
        $id   = explode(',',$_REQUEST['ids']);
        $type = $_GET['type'] == 'y' ? 1 : -1;
        $news = M('tbnews');
        $data['Statusdescription']  = $type;
        foreach($id AS $k => $v){
            $where['ID'] = $v;
            $news->where($where)->save($data);
        }
        $this->success(L('更新成功'));
    }

    public function checkedit(){
    
    	// 此方法的权限ID为56
    	$model = M('tbnews');
    	$id = I('post.ID',0);
    	$data['Statusdescription'] = I('post.Statusdescription','');
    	$res = $model->where(array('ID'=>$id))->save($data); // 根据条件更新记录
    	//var_dump($model->getLastSql());exit;
    	 if($res){
    		$this->success('保存成功！');
    	 }else{

    	 	$this->error(L('保存成功'));
    	 }
    }
    /*执行新闻内容审核后的数据更新
     */
    public function chupdate(){

        // 此方法的权限ID为56
        $this->checkLevel(56);
        parent::update();
    }

    /*
     *图片上传，与编辑器中的图片上传共用
     */
    public function doUpload(){

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize =3145728 ;// 设置附件上传大小 
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  = './Public/Uploads/'; // 设置附件上传目录
        $upload->autoSub = false; //关闭自动生成子目录
         
        // 上传文件 
        $info = $upload->upload();
        //准备响应信息
        $res=array("err"=>"","msg"=>"");
        $res['state']=0;
        // $res['type']="";
        if(!$info){
           // 上传错误提示错误信息
           $res['err']="上传失败：原因:".$upload->getError();
        }else{
           foreach($info as $upfile){
               // 上传成功
               $res['msg']=__ROOT__."/Public/Uploads/".$upfile['savename'];
               $res['state']=200;
           }
        }
        // 将数据以ajax返回
        $this->ajaxReturn($res);
    }

    /*执行删除操作，支持单个和多个删除
     */
    public function del() {
        $this->checkLevel(57);

        $model = M('tbnews');
        if (!empty($model)) {
            $id = $_REQUEST['ids']; 
            if (isset($id) && is_string($id)) {
                //批量删除
                $condition = array('ID in ('.$id.')');
                if (false !== $model->where($condition)->delete()) {
                    $this->success(L('删除成功'));
                } else {
                    $this->error(L('删除失败'));
                }
            } else if (is_array($id)){
                 //批量删除
                $condition = array('ID in ('.explode(',', $id).')');
                if (false !== $model->where($condition)->delete()) {
                    $this->success(L('删除成功'));
                } else {
                    $this->error(L('删除失败'));
                }
            }else {
                $this->error('非法操作');
            }
        }
    }

    
}