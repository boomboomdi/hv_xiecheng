// 定义常用的校验，常用的正则 https://www.open-open.com/code/view/1430625516632
layui.define(['jquery'], function (exports) {
    var $ = layui.jquery;
    exports('validate', {
        isChannelRate: function (value, item) {
            var result = '';
            var regExp = new RegExp("^[0-9]{1}(.[0-9]{1,2})?$");
            if (isEmpty(value)) {
                result = '费率不能为空';
            }else {
                var arr = new Array();
                arr = value.split('-');
                if (arr.length == 2) {
                    var arr2 = new Array();
                    arr2 = arr[1].split(',');
                    if (arr2.length != 2){
                        result = '通道费率格式不正确2';
                    }
                }
            }
            if (!isEmpty(result)) {
                return result;
            }
        },
        isWeight: function (value, item) {
            var result = '';
            if (isEmpty(value)) {
                result = '权重值不能为空';
            }else {
                if (!new RegExp("^[1-9]{1}$").test(value)) {
                    result = '权重值必须为1-9之间的数字';
                }
            }
            if (!isEmpty(result)) {
                return result;
            }
        },
        isRate: function (value, item) {
            var result = '';
            if (isEmpty(value)) {
                result = '费率不能为空';
            }
            if (!isEmpty(result)) {
                return result;
            }
        },
        username: function (value, item) {
            if (!isEmpty(value)) {
                var result = '';
                $.ajax({
                    url: ctx + 'user/check/' + value,
                    data: {
                        "userId": item.getAttribute('id')
                    },
                    async: false,
                    type: 'get',
                    success: function (d) {
                        (!d) && (result = '该用户名已存在')
                    }
                });
                if (!isEmpty(result)) {
                    return result;
                }
            }
        },
        cron: function (value, item) {
            if (!isEmpty(value)) {
                var result = '';
                $.ajax({
                    url: ctx + 'job/cron/check',
                    data: {
                        "cron": value
                    },
                    async: false,
                    type: 'get',
                    success: function (d) {
                        (!d) && (result = 'cron表达式不合法')
                    }
                });
                if (!isEmpty(result)) {
                    return result;
                }
            }
        },
        email: function (value) {
            if (!isEmpty(value)) {
                if (!new RegExp("^\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*$").test(value)) {
                    return '请填写正确的邮箱';
                }
            }
        },
        phone: function (value) {
            if (!isEmpty(value)) {
                if (!new RegExp("^1\\d{10}$").test(value)) {
                    return '请填写正确的手机号码';
                }
            }
        },
        number: function (value) {
            if (!isEmpty(value)) {
                if (!new RegExp("^[0-9]*$").test(value)) {
                    return '只能填写数字';
                }
            }
        },
        range: function (value, item) {
            var minlength = item.getAttribute('minlength') ? item.getAttribute('minlength') : -1;
            var maxlength = item.getAttribute('maxlength') ? item.getAttribute('maxlength') : -1;
            var length = value.length;
            if (minlength === -1) {
                if (length > maxlength) {
                    return '长度不能超过 ' + maxlength + ' 个字符';
                }
            } else if (maxlength === -1) {
                if (length < minlength) {
                    return '长度不能少于 ' + minlength + ' 个字符';
                }
            } else {
                if (length > maxlength || length < minlength) {
                    return '长度范围 ' + minlength + ' ~ ' + maxlength + ' 个字符';
                }
            }
        }
    });

    function isEmpty(obj) {
        return typeof obj == 'undefined' || obj == null || obj === '';
    }
});