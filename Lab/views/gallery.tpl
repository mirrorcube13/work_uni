{EXTENDS="main"}

{SECTION="page_title"}
    {CV="app_header"}
{ENDSECTION}

{SECTION="content"}
    <div id="images">
        {CYCLE="images"; FILE="nano/image"}
    </div>
{ENDSECTION}

{SECTION="right_bar"}
    <form action="/" method="post" id="image-form" enctype="multipart/form-data">
        <label for="photo">Загрузить фото</label>
        <input type="file" name="photo" id="photo" accept="{DV="extensions"}">
        <label for="annotation">Аннотация к изображению</label>
        <input type="text" name="title" id="annotation">
        <label for="img-alt">Альтернативный текст</label>
        <input type="text" name="img-alt" id="img-alt">
        <button id="key-btn" type="submit">Загрузить фото</button>
    </form>
{ENDSECTION}