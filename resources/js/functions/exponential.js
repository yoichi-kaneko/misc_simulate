let formatter = new Intl.NumberFormat('ja-JP');

export function initializeExponentialForms()
{
    $('.form-exponential-value').each(function(element){
        let name = $(this).data('name');
        let chk_tmpl = $('#exponentialCheckboxTemplate').render({name: name});
        let label = $(this).find('label:first');
        label.append(chk_tmpl);

        let form_tmpl = $('#exponentialInputFormTemplate').render({name: name});
        let form = $(this).find('input.form-control:first');
        form.attr('data-name', name);
        form.after(form_tmpl);

        exchangeToExp(name);
    });

    $('.check_exp').click(function (){
        let name = $(this).data('name');

        if($(this).prop('checked')) {
            exchangeToExp(name);
        } else {
            exchangeToInt(name);
        }
    });
    $('.form_exp .form-control').keyup(function (){
        let name = $(this).data('name');
        changeExpVal(name);
    });
    $('.form_exp .form-control').change(function (){
        let name = $(this).data('name');
        changeExpVal(name);
    });
    return;
}

/**
 * 表示を指数計算方式に切り替える
 * @param name
 */
export function exchangeToExp(name)
{
    let int_val = $('.form-control[data-name=' + name + ']:first').val();
    let exp_val = getExpVal(int_val);
    let rounded_int_val = getIntVal(exp_val);
    $('.form_exp[data-name=' + name + '] .form-control').val(exp_val);
    $('.form_exp[data-name=' + name + '] .display_amount').html(formatter.format(rounded_int_val));
    $('.form-control[data-name=' + name + ']:first').val(rounded_int_val);

    $('.form-control[data-name=' + name + ']:first').hide();
    $('.form_exp[data-name=' + name + ']').show();
}

/**
 * 表示を整数方式に切り替える
 * @param name
 */
function exchangeToInt(name)
{
    $('.form-control[data-name=' + name + ']:first').show();
    $('.form_exp[data-name=' + name + ']').hide();
}

/**
 * 指数の数値が変更された時の再計算
 * @param name
 */
function changeExpVal(name)
{
    let exp_val = $('.form_exp .form-control[data-name=' + name + ']:first').val();
    let int_val;
    if (!Number.isInteger(parseInt(exp_val))) {
        int_val = 'Nan';
    } else if (exp_val < 0) {
        int_val = 'Nan';
    } else {
        int_val = 1;
        for (let i = 0; i < exp_val; i++) {
            int_val *= 2;
        }
    }
    if (!Number.isInteger(parseInt(int_val))) {
        $('.form_exp[data-name=' + name + '] .display_amount').html(int_val);
        $('.form-control[data-name=' + name + ']:first').val(int_val);
    } else {
        $('.form_exp[data-name=' + name + '] .display_amount').html(formatter.format(int_val));
        $('.form-control[data-name=' + name + ']:first').val(int_val);
    }
}

/**
 * 整数から指数を計算する
 * @param int_val
 */
export function getExpVal(int_val)
{
    if (!Number.isInteger(parseInt(int_val))) {
        return 0;
    }
    if (int_val <= 0) {
        return 0;
    }
    let i = 0;
    let amount = 1;
    while (amount < int_val) {
        amount *= 2;
        i++;
    }
    if (amount > int_val) {
        i--;
    }
    return i;
}

function getIntVal(exp_val)
{
    if (!Number.isInteger(parseInt(exp_val))) {
        return 1;
    }
    if (exp_val <= 0) {
        return 1;
    }
    let amount = 1;
    for (let i = 0; i < exp_val; i++) {
        amount *= 2;
    }
    return amount;
}
