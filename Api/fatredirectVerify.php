<!DOCTYPE HTML>
<html>
    <head>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <title>FATRedirect Verification</title>
    </head>
    <body class="container bg-secondary">
        <div class="row justify-content-center">
            <div class="card shadow mt-5 col-sm-8 col-12">
                <div id="error" class="d-none">
                    <p class="text-center text-white bg-danger border rounded">Sorry, you have to be of age to pay with FATRedirect</p>
                </div>
                <div class="card-body row justify-content-center">
                    <form id="form" method="post" class="col-12 col-sm-6">
                        <div class="mb-3">
                            <label for="birthday" class="form-label">Whats your birthday?</label>
                            <input id="birthday" name="birthday" type="date" class="form-control">
                        </div>
                        <div>
                            <input type="submit" class="btn btn-outline-secondary">
                        </div>
                        <input type="hidden" name="orderId" value="<?= $_REQUEST['orderId'] ?>">
                        <input type="hidden" name="fnc" value="fcFinalizeFatRedirect">
                    </form>
                </div>
            </div>
        </div>
        <input id="refererUrl" type="hidden" name="refererUrl" value="<?= $_SERVER['HTTP_REFERER'] ?>">
        <script src="./fatredirectVerify.js"></script>
    </body>
</html>