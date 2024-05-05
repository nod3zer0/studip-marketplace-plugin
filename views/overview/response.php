<?

/**
 * @author Rene Ceska <xceska06@stud.fit.vutbr.cz>
 */
?>

<form data-dialog="reload-on-close" method="post" action="<?= $controller->link_for('overview/send_response', $demand_id) ?>">
    <fieldset data-open="bd_basicsettings">
        <div style="text-align:center;">
            <h2>Message</h2>
            <textarea style="width: 100%;height: 250px; box-sizing: border-box;" name="message" id="message"></textarea>

        </div>
    </fieldset>
    <footer data-dialog-button>
        <button type="submit">Send</button>
    </footer>
</form>