<div class="row justify-content-center">
    <div class="card shadow mt-5 col-sm-8 col-12">
        <div id="error" class="<?= $this->getError() ? '' : 'd-none' ?>">
            <p class="text-center text-white bg-danger border rounded"><?= $this->getError() ?></p>
        </div>
        <div class="card-body row justify-content-center">
            <form id="form" method="post" class="col-12 col-sm-6">
                <div class="mb-3">
                    <label for="birthday" class="form-label">Whats your birthday?</label>
                    <input id="birthday" name="birthday" type="date" class="form-control" required>
                </div>
                <input type="hidden" name="fnc" value="verify">
                <input type="hidden" name="refererUrl" value="<?= $_SESSION['refererUrl'] ?>">
                <div>
                    <input type="submit" class="btn btn-outline-secondary">
                </div>
            </form>
        </div>
    </div>
</div>
<?php print_r($_SESSION) ?>