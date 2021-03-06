<?php
class CalorieMeasurementsView {
    
    public static function show() {
        HeaderView::show("Calorie Measurements");
        CalorieMeasurementsView::showBody();
        FooterView::show();
    }
    
    public static function edit() {
        HeaderView::show("Calorie Measurements | Edit");
        CalorieMeasurementsView::editBody();
        FooterView::show();
    }
    
    public static function showBody() {
        if (!isset($_SESSION) || !isset($_SESSION['measurements']) || !isset($_SESSION['profile'])):
            ?><p>Error: measurements not found</p><?php
            return;
        endif;
        $measurements = $_SESSION['measurements'];
        ?>
        
<section>
    <h2><a id="calories">Calorie Measurements</a></h2>
    <?php
        if (!isset($measurements["calories"]) || empty($measurements["calories"])):
            ?><p>No calorie measurements to show yet</p><?php
        else: ?>
    <table>
        <tr>
            <th>Calories</th>
            <th>Date / Time</th>
            <th>Notes</th>
            <th colspan="2">Actions</th>
        </tr><?php
            foreach ($measurements["calories"] as $calories):
                ?>
        <tr>
                <td><?=$calories->getMeasurement()?></td>
                <td><?=$calories->getDate() . ' / ' . $calories->getTime()?></td>
                <td><?=$calories->getNotes()?></td>
                <td>
                	<form action="measurements_edit_show_calories_<?=$calories->getDateTime()->format('Y-m-d H-i')?>" method="post">
                		<input type="submit" value="Edit" />
                	</form>
            	</td>
            	<td>
                	<form action="measurements_delete_<?=$calories->getDateTime()->format('Y-m-d H-i')?>" method="post">
                		<input type="submit" value="Delete" disabled="disabled" />
                	</form>
            	</td>
        </tr>
<?php   endforeach; ?>
    </table>
    <hr />
<?php   endif; ?>
    <form action="measurements_add_calories" method="post">
    	<fieldset>
    		<legend>Add a measurement</legend>
        	Calories Consumed <input type="text" name="calories" size="10" autofocus="autofocus" required="required" maxlength="4" tabindex="1" pattern="^[0-9]+$" /><br />
            Date <input type="date" name="date" required="required" tabindex="2" title="mm/dd/yyyy or mm-dd-yyyy" /><br />
            Time <input type="time" name="time" required="required" tabindex="3" title="H:M" /><br />
            Notes <input type="text" name="notes" size="30" maxlength="50" tabindex="4" /><br />
        	<input type="submit" value="Add" tabindex="5" />
            <input type="hidden" name="userName" value="<?=$_SESSION['profile']->getUserName()?>" tabindex="6" />
    	</fieldset>
    </form>
</section>

        <?php
        unset($_SESSION['measurements']['calories']);
    }
    
    public static function editBody() {
        if (!isset($_SESSION) || !isset($_SESSION['measurement']) || !isset($_SESSION['profile'])):
            ?><p>Error: unable to show measurements. Data is missing.</p><?php
            return;
        endif;
        
        $measurement = $_SESSION['measurement'];
        ?>
        
<section>
    <h2><a id="calories">Edit Calorie Measurement</a></h2>
 	<form action="measurements_edit_post_calories" method="post">
    	<fieldset>
    		<legend>Edit Measurement</legend>
        	Calories Consumed <input type="text" name="calories" value="<?=$measurement->getMeasurement()?>" size="10" autofocus="autofocus" required="required" maxlength="4" tabindex="1" pattern="^[0-9]+$" /><br />
            Date <input type="date" name="date" value="<?=$measurement->getDate()?>" required="required" tabindex="3" title="mm/dd/yyyy or mm-dd-yyyy" /><br />
            Time <input type="time" name="time" value="<?=$measurement->getTime()?>" required="required" tabindex="4" title="H:M" /><br />
            Notes <input type="text" name="notes" value="<?=$measurement->getNotes()?>" size="30" maxlength="50" tabindex="5" /><br />
        	<input type="submit" value="Save Changes" tabindex="6" />
            <input type="hidden" name="userName" value="<?=$_SESSION['profile']->getUserName()?>" tabindex="7" />
    	</fieldset>
    </form>
</section>
        
        <?php
    }
    
}
?>