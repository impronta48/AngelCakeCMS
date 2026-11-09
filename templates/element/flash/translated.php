<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="modal d-block" tabindex="-1" role="dialog" style="background: rgba(0, 0, 0, .5)" onclick="if (event.target === this) this.remove()">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __('Contenuto tradotto in inglese') ?></h5>
            </div>
            <div class="modal-body"><?= $message ?></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="this.closest('.modal').remove()">OK</button>
            </div>
        </div>
    </div>
</div>
