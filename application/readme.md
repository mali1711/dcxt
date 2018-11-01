```
<div id="mainnav-menu-wrap">
                        <div class="nano">
                            <div class="nano-content">
                                <?php 
                                    $UserId = cookie('AdminUserId');
                                    $UserInfo = D('AdminUsers')->where("id=$UserId")->find();
                                    $role = rtrim($UserInfo['role'],',');
                                    $res = M('column')->where("id in($role)")->select();
                                    function GetTree($m,$name='child',$p_id = 0) {
                                        $arr = array();
                                        foreach ($m as $v) {
                                            if ($v['pid'] == $p_id) {
                                                $v['child'] = GetTree($m, $name, $v['id']);
                                                $arr[] = $v;
                                            }
                                        }
                                        return $arr;
                                    }
                                    $column = GetTree($res,$name='child',$p_id = 0);
                                 ?>
                                <!--Profile Widget-->
                                <!--================================-->
                                <div id="mainnav-profile" class="mainnav-profile">
                                    <div class="profile-wrap">
                                        <div class="pad-btm">
                                            <img class="img-circle img-sm img-border" src="/Public/Admin/img/profile-photos/1.png" title="管理员头像" alt="Profile Picture">
                                        </div>
                                        <a href="/Admin/#profile-nav" class="box-block" data-toggle="collapse" aria-expanded="false">
                                            <span class="pull-right dropdown-toggle">
                                                <i class="dropdown-caret"></i>
                                            </span>
                                            <p class="mnp-name">{$UserInfo.nickname}</p>
                                        </a>
                                    </div>
                                    <div id="profile-nav" class="collapse list-group bg-trans">
                                        <a href="<?=C('CHILDREN');?>/Admin/Administrator/password" class="list-group-item">
                                            <i class="ion-ios-gear icon-lg icon-fw"></i> 修改密码
                                        </a>
                                        <a href="javascript:;" id="LoginOutNow" class="list-group-item">
                                            <i class="demo-pli-unlock icon-lg icon-fw"></i> 退出
                                        </a>
                                    </div>
                                </div>
                                <ul id="mainnav-menu" class="list-group">
                                    <li class="active-link">
                                        <a href="<?=C('CHILDREN');?>/Admin/Column">
                                            <i class="demo-psi-home"></i>
                                            <span class="menu-title">
                                                <strong>栏目管理</strong>
                                            </span>
                                        </a>
                                    </li>
                                    <?php foreach($column as $val){ ?>
                                    <!--Category name-->
                                    <li class="list-header">{$val.name}</li>
                                    <!--Menu list item-->
                                    <?php foreach($val['child'] as $cal){ ?>
                                        <li>
                                            <a href="<?php if($cal['url'] != ''){echo C('CHILDREN');}?>{$cal.url}">
                                                <i class="{$cal.icon}"></i>
                                                <span class="menu-title">
                                                    <strong>{$cal.name}</strong>
                                                </span>
                                                <if condition="!empty($cal['child'])">
                                                <i class="arrow"></i>
                                                </if>
                                            </a>
                                            <!--Submenu-->
                                            <?php foreach($cal['child'] as $eal){ ?>
                                            <ul class="collapse">
                                                <li><a href="<?=C('CHILDREN');?>{$eal.url}">{$eal.name}</a></li>
                                            </ul>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>

```