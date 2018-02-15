function showCopyrightYear(begin) {
    var year = (new Date()).getFullYear();
    if (begin < year) return (begin + ' - ' + year + ' &copy');
    else return(year + ' &copy');
}

function checkAnswers() {
    $('input[type=radio].correct').attr('checked', true);
    $('input[type=checkbox].correct').attr('checked', true);
}