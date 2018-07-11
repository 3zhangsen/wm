<?php
         $sql = "
            CREATE TABLE IF NOT EXISTS `$usr_name` (
            `id` int(100) NOT NULL AUTO_INCREMENT,
            `ip` varchar(40) ,
            `url` varchar(100),
            `dic` varchar(250) COMMENT '描述',
            `usr_name` varchar(100),
            `edit_time` datetime,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4";
           $usrsql=db()->execute($sql);
          $isTable =db()->query("SHOW TABLES LIKE '$usr_name'");

function normalReceive(hid, uk) {
    if (Cookies.get('token')) {
        receiveData(hid, uk, '', '');
        return;
    }
    var captcha = new TencentCaptcha('2039931201', function(res) {
        if (res.ret == 0) {
            receiveData(hid, uk, res.ticket, res.randstr);
        } else {
            $('.modal').addClass('is-active')
            $('#denytips').removeClass('is-hidden')
            $('#denymsg').html("登录后可免验证领取红包。")
        }
    });
    captcha.show();
}
function receiveData(hid, uk, ticket, randstr) {
    var captcha = {
        ticket: ticket,
        randstr: randstr
    };
    $.ajax({
        url: '/normal/receive/' + hid + '/' + uk,
        type: 'POST',
        dataType: "json",
        contentType: "application/json",
        data: JSON.stringify(captcha),
        success: function (result) {
            if (navigator.userAgent.toLowerCase().match(/MicroMessenger/i) == "micromessenger") {
                window.location.href = result.info
            } else {
                window.open(result.info)
            }
        },
        error: function () {
            $('.modal').addClass('is-active')
            $('#denytips').removeClass('is-hidden')
            $('#denymsg').html(result.message)
        }
    })
}

          ?>