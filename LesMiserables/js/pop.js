// JavaScript Document

$(document).ready(function(){

//電影介紹
$('#introduction_btn').click(function(){   //给box_btn绑定一个鼠标点击的事件
        $.unblockUI({onUnblock:function(){
        $.blockUI({    //当点击事件发生时调用弹出层
             message: $('#introduction'),    //要弹出的元素box
             css: {    //弹出元素的CSS属性
             top: '25%',
		 	 left: '70%',
		 	 textAlign: 'left',
		 	 marginLeft: '-320px',
		     marginTop: '-145px',
             width: '600px',
			 height: '80%',
             background: 'none'
            }
        });
        $('.blockOverlay').attr('title','點擊一下即可關閉').click(function(){$.unblockUI();$('li a').removeClass('active');$('li a:first').addClass('active')});  //遮罩层属性的设置以及当鼠标单击遮罩层可以关闭弹出层
        $('.close').click($.unblockUI);  //也可以自定义一个关闭按钮来关闭弹出层
        $('.mCSB_container').resize();
    }})
});


//活動辦法
$('#method_btn').click(function(){   //给box_btn绑定一个鼠标点击的事件
        $.unblockUI({onUnblock:function(){
        $.blockUI({    //当点击事件发生时调用弹出层
             message: $('#method'),    //要弹出的元素box
             css: {    //弹出元素的CSS属性
             top: '25%',
		 	 left: '70%',
		 	 textAlign: 'left',
		 	 marginLeft: '-320px',
		     marginTop: '-145px',
             width: '600px',
			 height: '80%',
             background: 'none'
            }
        });
        $('.blockOverlay').attr('title','點擊一下即可關閉').click(function(){$.unblockUI();$('li a').removeClass('active');$('li a:first').addClass('active')});  //遮罩层属性的设置以及当鼠标单击遮罩层可以关闭弹出层
        $('.close').click($.unblockUI);  //也可以自定义一个关闭按钮来关闭弹出层
        $('.mCSB_container').resize();
    }})
});


//活動留言
$('div#message_btn, a#message_btn').click(function(){   //给box_btn绑定一个鼠标点击的事件
        $.unblockUI({onUnblock:function(){
        $.blockUI({    //当点击事件发生时调用弹出层
             message: $('#message'),    //要弹出的元素box
             css: {    //弹出元素的CSS属性
             top: '25%',
		 	 left: '70%',
		 	 textAlign: 'left',
		 	 marginLeft: '-320px',
		     marginTop: '-145px',
             width: '600px',
			 height: '80%',
             background: 'none'
            }
        });
        $('.blockOverlay').attr('title','點擊一下即可關閉').click(function(){$.unblockUI();$('li a').removeClass('active');$('li a:first').addClass('active')});  //遮罩层属性的设置以及当鼠标单击遮罩层可以关闭弹出层
        $('.close').click($.unblockUI);  //也可以自定义一个关闭按钮来关闭弹出层
        $('.mCSB_container').resize();
    }})
});



//得獎名單
$('#winners_btn').click(function(){   //给box_btn绑定一个鼠标点击的事件
        $.unblockUI({onUnblock:function(){
        $.blockUI({    //当点击事件发生时调用弹出层
             message: $('#winners'),    //要弹出的元素box
             css: {    //弹出元素的CSS属性
             top: '25%',
		 	 left: '70%',
		 	 textAlign: 'left',
		 	 marginLeft: '-320px',
		     marginTop: '-145px',
             width: '600px',
			 height: '80%',
             background: 'none'
            }
        });
        $('.blockOverlay').attr('title','點擊一下即可關閉').click(function(){$.unblockUI();$('li a').removeClass('active');$('li a:first').addClass('active')});  //遮罩层属性的设置以及当鼠标单击遮罩层可以关闭弹出层
        $('.close').click($.unblockUI);  //也可以自定义一个关闭按钮来关闭弹出层
        $('.mCSB_container').resize();
    }})
});
})