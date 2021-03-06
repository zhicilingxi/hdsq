<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="utf-8" />
<title>hzcms安装</title>
<link rel="stylesheet" href="/Public/install/simpleboot/themes/flat/theme.min.css" />
<link rel="stylesheet" href="/Public/install/css/install.css" />
<link rel="stylesheet" href="/Public/font-awesome/css/font-awesome.min.css" type="text/css">


    <script type="text/javascript">
        //全局变量
        var GV = {
            ROOT: "/",
            WEB_ROOT: "/",
            JS_ROOT: "PUBLIC/js/"
        };
    </script>
    <script src="/Public/js/jquery.min.js"></script>
    <script src="/Public/js/wind.js"></script>
    <script type="text/html" id="exec-success-msg-tpl">
        <li>
            <i class="fa fa-check correct"></i>
            {message}<br>
            <!--<pre>{sql}</pre>-->
        </li>
    </script>
    <script type="text/html" id="exec-fail-msg-tpl">
        <li>
            <i class="fa fa-remove error"></i>
            {message}<br>
            <pre>{sql}</pre>
            <!--<pre>{exception}</pre>-->
        </li>
    </script>
</head>
<body>
<div class="wrap">
    <div class="header">
	<h1 class="logo">HDSQ 安装向导</h1>
	<div class="version">1.0</div>
</div>
    <section class="section">
        <div class="step">
            <ul class="unstyled">
                <li class="on"><em>1</em>检测环境</li>
                <li class="on"><em>2</em>创建数据</li>
                <li class="current"><em>3</em>完成安装</li>
            </ul>
        </div>
        <div class="install" id="log">
            <ul id="install-msg-panel" class="unstyled"></ul>
        </div>
        <div class="bottom text-center">
            <a href="javascript:;"><i class="fa fa-refresh fa-spin"></i>&nbsp;正在安装...</a>
        </div>
    </section>
    <script type="text/javascript">
        $(function () {
            install(0);
        });

        Wind.use("noty", function () {

        });

        var $installMsgPanel = $('#install-msg-panel');
        var $log             = $("#log");
        var execSuccessTpl   = $('#exec-success-msg-tpl').html();
        var execFailTpl      = $('#exec-fail-msg-tpl').html();
        var sqlExecResult;

        function install(sqlIndex) {
            $.ajax({
                url: "/index/install/",
                data: {sql_index: sqlIndex},
                dataType: 'json',
                type: 'post',
                success: function (data) {

                    console.log(data);
                    var line = sqlIndex + 1;
                    if (data.statusCode == 200) {

                        if (!(data.done)) {
                            var tpl = execSuccessTpl;
                           // tpl     = tpl.replace(/\{message}/g, line + '.' + data.message);
                            tpl     = '执行sql'+line+'<br/>';
                            //tpl     = tpl.replace(/\{sql}/g, data.sql);
                            $installMsgPanel.append(tpl);

                        } else {
                            $installMsgPanel.append('<li><i class="fa fa-check correct"></i>数据库安装完成!</li>');

                            sqlExecResult = data;
                            if (data.error !=0) {
                                noty({
                                    text: "安装过程,共" + data.error + "个SQL执行错误,可能您在此数据库下已经安装过 hdsq,请查看问题后重新安装,或者<br>"
                                    + '<a target="_blank" href="https://github.com/hzcms/hzcms/issues">反馈问题</a>',
                                    type: 'confirm',
                                    layout: "center",
                                    timeout: false,
                                    modal: true,
                                    buttons: [
                                        {
                                            addClass: 'btn btn-primary',
                                            text: '确定',
                                            onClick: function ($noty) {
                                                $noty.close();

                                                //setDbConfig();

                                            }
                                        },
                                        {
                                            addClass: 'btn btn-danger',
                                            text: '取消',
                                            onClick: function ($noty) {
                                                $noty.close();

                                            }
                                        }
                                    ]
                                });
                            } else {
                                setDbConfig();
                            }
                        }

                    } else if (data.code == 0) {

                        var tpl = execFailTpl;
                        tpl     = tpl.replace(/\{message}/g, line + '.' + data.message);
                        tpl     = tpl.replace(/\{sql}/g, data.data.sql);
                        tpl     = tpl.replace(/\{exception}/g, data.data.exception);
                        $installMsgPanel.append(tpl);
                    }

                    $log.scrollTop(1000000000);

                    if (!(data && data.done)) {
                        sqlIndex++;
                        install(sqlIndex);
                    }


                },
                error: function () {

                },
                complete: function () {

                }
            });
        }

        function setDbConfig() {
            $.ajax({
                url: "/index/setDbConfig/",
                dataType: 'json',
                data:{_hithinkcmf:1},
                type: 'post',
                success: function (data) {
                    if (data.statusCode == 200) {
                        $installMsgPanel.append('<li><i class="fa fa-check correct"></i>' + data.message + '</li>');
                        $log.scrollTop(1000000000);
                        setSite();
                    } else {
                        $installMsgPanel.append('<li><i class="fa fa-remove error"></i>' + data.message + '</li>');
                        $log.scrollTop(1000000000);
                        noty({
                            text: data.message + ',请检查 Common/Conf/config.php 是否可写!',
                            type: 'confirm',
                            layout: "center",
                            timeout: false,
                            modal: true,
                            buttons: [
                                {
                                    addClass: 'btn btn-primary',
                                    text: '重试',
                                    onClick: function ($noty) {
                                        $noty.close();
                                        setDbConfig();
                                    }
                                },
                                {
                                    addClass: 'btn btn-danger',
                                    text: '取消',
                                    onClick: function ($noty) {
                                        $noty.close();
                                    }
                                }
                            ]
                        });
                    }
                },
                error: function () {

                }
            });
        }

        function setSite() {
            $.ajax({
                url: "/index/setSite/",
                dataType: 'json',
                data:{_hithinkcmf:1},
                type: 'post',
                success: function (data) {

                    if (data.statusCode == 200) {
                        $installMsgPanel.append('<li><i class="fa fa-check correct"></i>' + data.message + '</li>');
                        $log.scrollTop(1000000000);
                        installTheme();
                    } else {
                        $installMsgPanel.append('<li><i class="fa fa-remove error"></i>' + data.message + '</li>');
                        $log.scrollTop(1000000000);
                        noty({
                            text: data.message,
                            type: 'confirm',
                            layout: "center",
                            timeout: false,
                            modal: true,
                            buttons: [
                                {
                                    addClass: 'btn btn-primary',
                                    text: '重试',
                                    onClick: function ($noty) {
                                        $noty.close();
                                        setSite();
                                    }
                                },
                                {
                                    addClass: 'btn btn-danger',
                                    text: '取消',
                                    onClick: function ($noty) {
                                        $noty.close();
                                    }
                                }
                            ]
                        });
                    }

                },
                error: function () {

                }
            });
        }

        function installTheme() {
            $.ajax({
                url: "/index/installTheme/",
                dataType: 'json',
                data:{_hithinkcmf:1},
                type: 'post',
                success: function (data) {

                    if (data.statusCode == 200) {
                        $installMsgPanel.append('<li><i class="fa fa-check correct"></i>' + data.message + '</li>');
                        $log.scrollTop(1000000000);
                        setTimeout(function () {
                            window.location = "/index/step5/";
                        }, 1000);
                    } else {
                        $installMsgPanel.append('<li><i class="fa fa-remove error"></i>' + data.message + '</li>');
                        $log.scrollTop(1000000000);
                        noty({
                            text: data.message,
                            type: 'confirm',
                            layout: "center",
                            timeout: false,
                            modal: true,
                            buttons: [
                                {
                                    addClass: 'btn btn-primary',
                                    text: '重试',
                                    onClick: function ($noty) {
                                        $noty.close();
                                        installTheme();
                                    }
                                },
                                {
                                    addClass: 'btn btn-danger',
                                    text: '取消',
                                    onClick: function ($noty) {
                                        $noty.close();
                                    }
                                }
                            ]
                        });
                    }

                },
                error: function () {

                }
            });
        }
    </script>
</div>
<div class="footer">
	&copy; 2017-<?php echo date('Y');?> <a href="http://www.bjhanzhuo.com" target="_blank">hzcms Team</a>
</div>
</body>
</html>