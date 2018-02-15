{EXTENDS="main"}

{SECTION="page_title"}
    {DV="f_title"}
{ENDSECTION}

{SECTION="content"}
    <h1 class="page-header">{DV="f_title"}</h1>
    <div class="links">
        <a href="/photo-edit/{DV="f_id"}">Редактировать</a>
        <a href="/photo-delete/{DV="f_id"}">Удалить</a>
    </div>
    <div class="gallery-photo">
        <img src="{DV="f_real_name"}" alt="{DV="f_alt"}" title="{DV="f_title"}">
        <div class="views big">
            <i></i><span>Просмотров: {DV="f_views"} </span>
        </div>
    </div>
{ENDSECTION}

