{EXTENDS="main"}

{SECTION="page_title"}
    {DV="f_title"}
{ENDSECTION}

{SECTION="content"}
<form action="/photo-edit/{DV="f_id"}" method="post" id="image-form">
    <table>
        <tr>
            <td><label for="annotation">Аннотация к изображению</label></td>
            <td><input type="text" name="img-title" id="annotation" value="{DV="f_title"}"></td>
        </tr>
        <tr>
            <td><label for="img-alt">Альтернативный текст</label></td>
            <td><input type="text" name="img-alt" id="img-alt" value="{DV="f_alt"}"></td>
        </tr>
    </table>
    <button id="key-btn" type="submit">Сохранить</button>
</form>
{ENDSECTION}