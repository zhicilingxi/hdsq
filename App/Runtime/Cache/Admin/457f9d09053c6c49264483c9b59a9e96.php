<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" action="/Admin/Webmenu/index" method="post">
    <input type="hidden" name="pageNum" value="<?php echo ((isset($currentPage) && ($currentPage !== ""))?($currentPage):'1'); ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
    <input type="hidden" name="_order" value="<?php echo ($_REQUEST['_order']); ?>"/>
    <input type="hidden" name="_sort" value="<?php echo ($_REQUEST['_sort']); ?>"/>
</form>
<div class="pageHeader">
    <form rel="pagerForm" onsubmit="return navTabSearch(this);" method="post">
        <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    
                </tr>
            </table>
        </div>
    </form>
</div>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a class="add" href="/Admin/Webmenu/add" target="navTab"  rel="addlinks" title="添加友情链接"><span>添加</span></a></li>
            <li><a class="delete" href="/Admin/Webmenu/delete_tag/id/<?php echo (C("TMPL_L_DELIM")); ?>item_id<?php echo (C("TMPL_R_DELIM")); ?>/navTabId/listlinks" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
            <li><a class="edit" href="/Admin/Webmenu/edit/id/<?php echo (C("TMPL_L_DELIM")); ?>item_id<?php echo (C("TMPL_R_DELIM")); ?>" rel="editlinks" target="navTab" title="编辑友情链接"><span>编辑</span></a></li>
            <li class="line">line</li>
            <li><a class="icon"  href="javascript:navTabPageBreak()"><span>刷新</span></a></li>
        </ul>
    </div>
    <table class="list" width="100%" layoutH="90">
        <thead>
            <tr>
                <!--  <th width="22"><input type="checkbox" group="ids" class="checkboxCtrl"></th> -->
                <th width="30" orderField="link_id" <?php if($_REQUEST['_order']== 'id'): ?>class="<?php echo ($_REQUEST['_sort']); ?>"<?php endif; ?>>ID</th>
       
        <th width="100" orderField="name">链接名</th>
        <th width="100" orderField="url">链接地址</th>
        <th width="100" >排序</th>
        <th width="80">操作</th>  
        </tr>
        </thead>
        <tbody>
        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="item_id" rel="<?php echo ($vo["id"]); ?>">
            <td><?php echo ($vo["id"]); ?></td>
            <td><?php echo ($vo["name"]); ?></td>
            <td><?php echo ($vo["url"]); ?></td>
            <td><?php echo ($vo["orderby"]); ?></td>    
            <td>
                <a href="/Admin/Webmenu/edit/id/<?php echo ($vo["id"]); ?>/navTabId/ads_link" class="edit" target="navTab" rel="editlinks" title="编辑链接"><span>编辑</span></a>&nbsp;
                <a href="/Admin/Webmenu/delete_tag/id/<?php echo ($vo["id"]); ?>" class="delete" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a>

            </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>

    <div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak(<?php echo (C("TMPL_L_DELIM")); ?>numPerPage:this.value<?php echo (C("TMPL_R_DELIM")); ?>)">
                <option value="10" <?php if($numPerPage == 10): ?>selected<?php endif; ?>>10</option>
                <option value="15" <?php if($numPerPage == 15): ?>selected<?php endif; ?>>15</option>
                <option value="20" <?php if($numPerPage == 20): ?>selected<?php endif; ?>>20</option>
                <option value="25" <?php if($numPerPage == 25): ?>selected<?php endif; ?>>25</option>
                <option value="30" <?php if($numPerPage == 30): ?>selected<?php endif; ?>>30</option>
            </select>
            <span>共<?php echo ($totalCount); ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="5" currentPage="<?php echo ($currentPage); ?>"></div>
    </div>
</div>
<script type="text/javascript">
            if ($("#pdindex").val() !== 2){
            $(".csindex").val(0).hide();
            }
            $(".dtstyle").val(0).hide();
            $(".dtkong").val(0).hide();
            $(".dtjubu").val(0).hide();
            $(".tucestyle").val(0).hide();
            $(".baojia").val(0).hide();
            $(".lixiangzhuang").val(0).hide();
            $(".gonglue").val(0).hide();
            $("#pdindex").change(function(){
    if ($("#pdindex").val() == 2){
        $(".csindex").show();
    } else if ($("#pdindex").val() == 6){
    var str = "<option value=''>全部</option>\n";
            str += "<option value='4'>同行业网站</option>\n";
            str += "<option value='5'>综合性网站</option>\n";
            str += "<option value='6'>地方性网站</option>\n";
            $("select[name='is_pic']").html(str);
            $(".csindexs").attr("class", "csindex");
            $(".csindex").hide();
    } else if ($("#pdindex").val() == 7){
    $(".dtstyle").show();
            $(".dtkong").val(0).hide();
            $(".dtjubu").val(0).hide();
            $(".tucestyle").val(0).hide();
            $(".baojia").val(0).hide();
            $(".lixiangzhuang").val(0).hide();
            $(".gonglue").val(0).hide();
    } else if ($("#pdindex").val() == 8){
    $(".dtkong").show();
            $(".dtstyle").val(0).hide();
            $(".dtjubu").val(0).hide();
            $(".tucestyle").val(0).hide();
            $(".baojia").val(0).hide();
            $(".lixiangzhuang").val(0).hide();
            $(".gonglue").val(0).hide();
    } else if ($("#pdindex").val() == 9){
    $(".dtjubu").show();
            $(".dtstyle").val(0).hide();
            $(".dtkong").val(0).hide();
            $(".tucestyle").val(0).hide();
            $(".baojia").val(0).hide();
            $(".lixiangzhuang").val(0).hide();
            $(".gonglue").val(0).hide();
    } else if ($("#pdindex").val() == 10){
    $(".tucestyle").show();
            $(".dtstyle").val(0).hide();
            $(".dtkong").val(0).hide();
            $(".dtjubu").val(0).hide();
            $(".baojia").val(0).hide();
            $(".lixiangzhuang").val(0).hide();
            $(".gonglue").val(0).hide();
    } else if ($("#pdindex").val() == 11){
    $(".baojia").show();
            $(".dtstyle").val(0).hide();
            $(".dtkong").val(0).hide();
            $(".dtjubu").val(0).hide();
            $(".tucestyle").val(0).hide();
            $(".lixiangzhuang").val(0).hide();
            $(".gonglue").val(0).hide();
    } else if ($("#pdindex").val() == 12){
    $(".lixiangzhuang").show();
            $(".dtstyle").val(0).hide();
            $(".dtkong").val(0).hide();
            $(".dtjubu").val(0).hide();
            $(".tucestyle").val(0).hide();
            $(".baojia").val(0).hide();
            $(".gonglue").val(0).hide();
    } else if ($("#pdindex").val() == 13){
    $(".gonglue").show();
            $(".dtstyle").val(0).hide();
            $(".dtkong").val(0).hide();
            $(".dtjubu").val(0).hide();
            $(".tucestyle").val(0).hide();
            $(".baojia").val(0).hide();
            $(".lixiangzhuang").val(0).hide();
    } else{
    var str = "<option value=''>全部</option>\n";
            str += "<option value='1'>友情链接</option>\n";
            str += "<option value='2'>合作伙伴</option>\n";
            str += "<option value='3'>热点链接</option>\n";
            $("select[name='is_pic']").html(str);
            $(".csindexs").attr("class", "csindex");
            $(".csindex").hide();
            $(".dtstyle").val(0).hide();
            $(".dtkong").val(0).hide();
            $(".dtjubu").val(0).hide();
            $(".tucestyle").val(0).hide();
            $(".baojia").val(0).hide();
            $(".lixiangzhuang").val(0).hide();
            $(".gonglue").val(0).hide();
    }

    })
</script>