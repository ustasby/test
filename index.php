<?php
/**
 * Place in the root, create a "data" folder with write permissions.
 *
 * @author Sergey Stepanov aka ustasby
 */
define('DATA_DIR', __DIR__ . '/data/');

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $url = file_get_contents('php://input');

    if (filter_var($url, FILTER_VALIDATE_URL)) {
        do {
            $size = 4;
            $short = make_short_url(++$size);
            if (file_exists(DATA_DIR . $short)) {
                $short = '';
            } else {
                file_put_contents(DATA_DIR . $short, $url);
            }
        } while (empty($short));
        echo $short;
    } else {
        echo 'error';
    }
    die();
}

$error = '';

if (isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'get') {
    if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
        $short = get_url(trim($_SERVER['REQUEST_URI'], '/'));
        if (!empty($short)) {
            if (file_exists(DATA_DIR . $short)) {
                header('Location: ' . file_get_contents(DATA_DIR . $short));
                exit;
            } else {
                $error = 'We searched, but did not find!';
            }
        }
    }
}

function get_url($pathname)
{
    $el = explode('/', $pathname);
    if (empty($el)) {
        return null;
    }
    return end($el);
}

function make_short_url($size = 5)
{
    $str = '1234567890QqWwEeRrTtYyUuIiOoPpAaSsDdFfGgJjHhKkLlXxZzCcVvBbNnMm';
    return substr(str_shuffle($str), 0, $size);
}

?>
<html>
<head>
    <meta charset="utf-8">
    <title>Задача XIAG-теста</title>
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport"
          content="width=device-width, user-scalable=yes, initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0">
    <style>
        body {
            color: #5E5E5E;
            font: 13px normal Arial, Helvetica, sans-serif;
        }

        html, body, div, span, object, iframe, h1, h2, h3, h4, h5, p, a, abbr, acronym, address, code, del, dfn, em, img, q, dl, dt, dd, ol, ul, li, fieldset, form, label, legend, caption {
            margin: 0;
            padding: 0;
        }

        .content {
            border: 1px solid silver;
            margin: 3em auto;
            width: 960px;
        }

        header {
            color: #174482;
            font-size: 26px;
            padding: 30px 20px 30px 100px;
        }

        table {
            background: #ECECEC;
            border: 1px solid silver;
            border-width: 1px 0;
            padding: 30px 20px 30px 100px;;
            color: #5E5E5E;
            width: 100%;
            text-align: left;
        }

        table td {
            width: 50%;
            white-space: nowrap;
        }

        th {
            text-align: left;
        }

        input {
            font-size: 16px;
        }

        footer {
            padding: 0.5em;
            color: #5E5E5E;
        }
    </style>
</head>
<body>
<div class="content">
    <header>URL shortener</header>
    <form action="" id="form">
        <table>
            <tr>
                <th>Long URL</th>
                <th>Short URL</th>
            </tr>
            <tr>
                <td>
                    <input type="url" name="url" id="url">
                    <input type="submit" value="Do!">
                </td>
                <td id="result"></td>
            </tr>
        </table>
    </form>
    <footer>
            <pre>
            Using this HTML please implement the following:

            1. Site-visitor (V) enters any original URL to the Input field, like
            http://anydomain/any/path/etc;
            2. V clicks submit button;
            3. Page makes AJAX-request;
            4. Short URL appears in Span element, like http://yourdomain/abCdE (don't use any
               external APIs as goo.gl etc.);
            5. V can copy short URL and repeat process with another link

            Short URL should redirect to the original link in any browser from any place and keep
            actuality forever, doesn't matter how many times application has been used after that.


            Requirements:

            1. Use PHP or Node.js;
            2. Don't use any frameworks.

            Expected result:

            1. Source code;
            2. System requirements and installation instructions on our platform, in English.
            </pre>

    </footer>
</div>

<script>

    <?php if (!empty($error)) echo "alert('{$error}');";?>


    document.getElementById('form').onsubmit = function () {
        var url = document.getElementById('url');

        var xhr = new XMLHttpRequest();

        xhr.open('POST', 'index.php', true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.send(url.value);
        xhr.onreadystatechange = function () { // (3)
            if (xhr.readyState != 4) {
                return;
            }
            if (xhr.status != 200) {
                alert('Everything is broken, we will not fix it soon!');
            } else {
                if (xhr.responseText == 'error') {
                    alert('Check the address');
                } else {
                    //var pathname = window.location.pathname;
                    //var link = window.location.origin + pathname.substring(0, pathname.lastIndexOf('/'))
                    //    + '/' + xhr.responseText;
                    var link = window.location.origin + '/' + xhr.responseText;
                    document.getElementById('result').innerHTML = '<span>' + link + '</span>';
                }
            }
        };
        return false;
    };
</script>
</body>
</html>
