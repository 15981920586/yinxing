/**
 * Created by Administrator on 2016/4/4.
 */


//批量更新排序
function SortAffirm(url)
{

    $('#MyForm').attr('action',url);
    //alert(url);
    $('#MyForm').submit();
}
//全选反选
function AllCheckBoxCheck(Obj)
{
    if(Obj.checked)
    {
        $("input[name='Del_ID[]']:checkbox").prop('checked',true);
    }else{
        $("input[name='Del_ID[]']:checkbox").prop('checked',false);
    }
}
//批量删除确认
function AllDelCheck(url)
{
    //var Count是获取有几个checkbox
    var Count=$("input[name='Del_ID[]']:checked").length;
    if(Count<=0)
    {
        alert("请您选择要删除的记录！");
        return false;
    }else{
        //假如路径不为空时(指onclick或者触发的其他js方法括号内没有传递路径)，
        // 则此时在触发提交命令时，让后面的url 替换前面的
if(confirm('您确认要删除这几条记录吗'))
{
        if(url!='')
        {
            $('#MyForm').attr('action',url);
        }
        //为空时，直接用定义的form的 action路径 提交
        $('#MyForm').submit();
    }
 }
}
//删除确认
function DelList(Url)
{
    if(confirm('您确认要删除此记录吗？'))
    {
        location.href=Url;
    }
}
//管理员添加数据验证
function AddAdminCheck()
{
    var Admin_Name=$('#Admin_Name').val().trim();
    var Admin_Pwd=$('#Admin_Pwd').val().trim();
    var Admin_AffirmPwd=$('#Admin_AffirmPwd').val().trim();
    if(Admin_Name=="")
    {
        alert("管理员应户名不能为空");
        return false;
    }
    if(Admin_Pwd=="")
    {
        alert("管理员密码不能为空");
        return false;
    }
    if(Admin_Pwd!=Admin_AffirmPwd)
    {
        alert("您两次输入的密码不一致")
        return false;
    }
}

//管理员密码输入 对与不对 的验证
function EditAdminCheck()
{
    var Admin_Pwd=$('#Admin_Pwd').val().trim();
    var Admin_AffirmPwd=$('#Admin_AffirmPwd').val().trim();

    if(Admin_Pwd=="")
    {
        alert("管理员密码不能为空!");
        return false;
    }
    if(Admin_AffirmPwd=="")
    {
        alert("管理员确认密码不能为空!");
        return false;
    }
    if(Admin_Pwd!=Admin_AffirmPwd)
    {
        alert("您两次输入的密码不一致")
        return false;
    }
}
//验证当前管理员名称是否已经存在  ，用ajax验证
function AdminNameCheck(Obj)
{
    if(Obj.value!="")
    {
        $.ajax({
                type:'POST',
                url:"./?Action=Admin&Method=AdminNameCheck",
                data:{'Admin_Name':Obj.value},
                dataType:"json",
                cache:false,
                success:function(Result)//Result是从数据库中查出的任意名字
                {
                    //alert(Result);
                    if(Result.NO==0)
                    {
                        $('#Msg1').html("此用户名可以注册");
                    }else
                    {
                        $('#Msg1').html("此用户名已被注册");
                    }
                }
            });
    }
}

