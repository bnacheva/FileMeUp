$(document).ready(function () {
    $(".ml").each(function(){
      $(this).html($(this).text().replace(/([^\x00-\x80]|\w)/g, "<span class='letter'>$&</span>"));
    });
    
    anime.timeline({loop: true})
      .add({
        targets: '.ml .letter',
        scale: [4,1],
        opacity: [0,1],
        translateZ: 0,
        easing: "easeOutExpo",
        duration: 950,
        delay: function(el, i) {
          return 70*i;
        }
      }).add({
        targets: '.ml',
        opacity: 0,
        duration: 1000,
        easing: "easeOutExpo",
        delay: 1000
      });
});
$(document).ready(function () {
    $(document).on('click', '#sign_up', function () {

        var html = `
            <h2>Регистрация</h2>
            <form id='sign_up_form'>
                <div class="form-group">
                    <label for="firstname">Име</label>
                    <input type="text" placeholder="Име" class="form-control" name="firstname" id="firstname" required />
                </div>
 
                <div class="form-group">
                    <label for="lastname">Фамилия</label>
                    <input type="text" placeholder="Фамилия" class="form-control" name="lastname" id="lastname" required />
                </div>
 
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" placeholder="Email" class="form-control" name="email" id="email" required />
                </div>
 
                <div class="form-group">
                    <label for="password">Парола</label>
                    <input type="password" placeholder="Парола" class="form-control" name="password" id="password" required />
                </div>
 
                <button type='submit' class='btn btn-primary'>Регистрация</button>
            </form>
            `;

        clearResponse();
        $('#content').html(html);
    });

    $(document).on('submit', '#sign_up_form', function () {

        var sign_up_form = $(this);
        var form_data = JSON.stringify(sign_up_form.serializeObject());

        $.ajax({
            url: "api/create_user.php",
            type: "POST",
            contentType: 'application/json',
            data: form_data,
            success: function (result) {
                $('#response').html("<div class='alert alert-success'>Успешна регистрация. Моля влезте в профила си.</div>");
                sign_up_form.find('input').val('');
            },
            error: function (xhr, resp, text) {
                $('#response').html("<div class='alert alert-danger'>Грешка при регистрация.</div>");
            }
        });

        return false;
    });

    $(document).on('click', '#login', function () {
        showLoginPage();
    });

    $(document).on('submit', '#login_form', function () {

        var login_form = $(this);
        var form_data = JSON.stringify(login_form.serializeObject());

        $.ajax({
            url: "api/login.php",
            type: "POST",
            contentType: 'application/json',
            data: form_data,
            success: function (result) {
                setCookie("jwt", result.jwt, 1);
                showHomePage();
                $('#response').html("<div class='alert alert-success'>Успешен вход в системата.</div>");

            },
            error: function (xhr, resp, text) {
                $('#response').html("<div class='alert alert-danger'>Грешка при вход. Невалидни данни.</div>");
                login_form.find('input').val('');
            }
        });

        return false;
    });
    $(document).on('click', '#home', function () {
        showHomePage();
        clearResponse();
    });

    $(document).on('click', "#files", function () {
        var jwt = getCookie('jwt');
        $.post("api/validate_token.php", JSON.stringify({ jwt: jwt })).done(function (result) {
            var html = `
            <form id='files_form'>
            <hr/>
            <div id="upload_files">
                <h2>Качи файл</h2>
                <div class="form-group">
                    <label for="filename">Име</label>
                    <input type="text" placeholder="Име" class="form-control" name="filename" id="filename" required />
                </div>
                <div class="form-group">
                    <label for="filename">Достъп до всички</label>
                    <input style="margin-top: -24px; margin-left: -400px;" type="checkbox" class="form-control" name="access" id="access" name="checked" checked />
                </div>
 
                <button type='submit' class='btn btn-primary'>Качи</button>
            </div>
            <hr/>
            <div id="my_files">
            <h2>Моите файлове</h2>
            </div>
            <hr/>
            <div id="download_files">
            <h2>Достъпни файлове</h2>
            </div>
            </form>
            `;

            clearResponse();
            $('#content').html(html);
        })

            .fail(function (result) {
                showLoginPage();
                $('#response').html("<div class='alert alert-danger'>Моля, влезте в системата.</div>");
            });
    });

    $(document).on('submit', '#files_form', function () {

        var files_form = $(this);
        var form_data = JSON.stringify(files_form.serializeObject());

        $.ajax({
            url: "api/upload_file.php",
            type: "POST",
            contentType: 'application/json',
            data: form_data,
            success: function (result) {
                $('#response').html("<div class='alert alert-success'>Успешнo качване на файл.</div>");
                files_form.find('input').val('');
            },
            error: function (xhr, resp, text) {
                $('#response').html("<div class='alert alert-danger'>Грешка при качване на файл.</div>");
            }
        });

        return false;
    });

    $(document).on('click', '#update_account', function () {
        showUpdateAccountForm();
    });

    $(document).on('submit', '#update_account_form', function () {

        var update_account_form = $(this);
        var jwt = getCookie('jwt');

        var update_account_form_obj = update_account_form.serializeObject()

        update_account_form_obj.jwt = jwt;

        var form_data = JSON.stringify(update_account_form_obj);

        $.ajax({
            url: "api/update_user.php",
            type: "POST",
            contentType: 'application/json',
            data: form_data,
            success: function (result) {

                $('#response').html("<div class='alert alert-success'>Акаунтът е update-нат успешно.</div>");
                setCookie("jwt", result.jwt, 1);
            },

            error: function (xhr, resp, text) {
                if (xhr.responseJSON.message == "Unable to update user.") {
                    $('#response').html("<div class='alert alert-danger'>Неуспешен update.</div>");
                }

                else if (xhr.responseJSON.message == "Access denied.") {
                    showLoginPage();
                    $('#response').html("<div class='alert alert-success'>Моля, влезте в профила си.</div>");
                }
            }
        });

        return false;
    });

    $(document).on('click', '#logout', function () {
        showLoginPage();
        $('#response').html("<div class='alert alert-info'>Успешен изход.</div>");
    });

    function clearResponse() {
        $('#response').html('');
    }

    function showLoginPage() {

        setCookie("jwt", "", 1);

        var html = `
<h2>Вход</h2>
<form id='login_form'>
 <div class='form-group'>
     <label for='email'>Email</label>
     <input type='email' class='form-control' id='email' name='email' placeholder='Email'>
 </div>

 <div class='form-group'>
     <label for='password'>Парола</label>
     <input type='password' class='form-control' id='password' name='password' placeholder='Парола'>
 </div>

 <button type='submit' class='btn btn-primary'>Вход</button>
</form>
`;

        $('#content').html(html);
        clearResponse();
        showLoggedOutMenu();
    }

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function showLoggedOutMenu() {
        $("#login, #sign_up").show();
        $("#logout").hide();
    }

    function showHomePage() {

        var jwt = getCookie('jwt');
        $.post("api/validate_token.php", JSON.stringify({ jwt: jwt })).done(function (result) {

            var html = `
<div class="card">
<div class="card-header">Добре дошли!</div>
<div class="card-body">
    <h5 class="card-title">Успешен вход в системата.</h5>
    <p class="card-text">Страниците няма да бъдат достъпни, докато не влезнете в системата.</p>
</div>
</div>
`;

            $('#content').html(html);
            showLoggedInMenu();
        })

            .fail(function (result) {
                showLoginPage();
                $('#response').html("<div class='alert alert-danger'>Моля, влезте в системата.</div>");
            });
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }

            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function showLoggedInMenu() {
        $("#login, #sign_up").hide();
        $("#logout").show();
    }

    function showUpdateAccountForm() {
        var jwt = getCookie('jwt');
        $.post("api/validate_token.php", JSON.stringify({ jwt: jwt })).done(function (result) {

            var html = `
<h2>Промяна на данните</h2>
<form id='update_account_form'>
    <div class="form-group">
        <label for="firstname">Име</label>
        <input type="text" class="form-control" name="firstname" id="firstname" required value="` + result.data.firstname + `" />
    </div>

    <div class="form-group">
        <label for="lastname">Фамилия</label>
        <input type="text" class="form-control" name="lastname" id="lastname" required value="` + result.data.lastname + `" />
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" name="email" id="email" required value="` + result.data.email + `" />
    </div>

    <div class="form-group">
        <label for="password">Парола</label>
        <input type="password" class="form-control" name="password" id="password" />
    </div>

    <button type='submit' class='btn btn-primary'>
        Запази данните
    </button>
</form>
`;

            clearResponse();
            $('#content').html(html);
        })

            .fail(function (result) {
                showLoginPage();
                $('#response').html("<div class='alert alert-danger'>Моля, влезте в системата.</div>");
            });
    }
    $.fn.serializeObject = function () {

        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
});