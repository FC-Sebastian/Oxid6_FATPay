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
                    <p class="text-center text-white bg-danger border rounded">You must be of age to pay with FatRedirect</p>
                </div>
                <div class="card-body row justify-content-center">
                    <form id="form" method="post" class="col-12 col-sm-6">
                        <div class="mb-3">
                            <label for="birthday" class="form-label">Whats your birthday?</label>
                            <input id="birthday" name="birthday" type="date" class="form-control" required>
                        </div>
                        <input type="hidden" name="fnc" value="fcFinalizeRedirect">
                        <input id="refererUrl" type="hidden" name="refererUrl" value="<?= urldecode($_REQUEST['refererUrl']) ?>">
                        <div>
                            <input type="submit" class="btn btn-outline-secondary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script src="fatredirectVerify.js"></script>
    </body>
</html>
