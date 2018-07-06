<section>
    <?php echo form_open();?>
    <div class="form-group">
        <label>
            Centro
        </label>
        <?=form_dropdown('centro',array(''=>' [ Elegir ]')+$centros,'','class="form-control"')?>
    </div>
    <div class="form-group">
        <label>
            Organizaciones
        </label>
        <?=form_dropdown('org_path',array(''=>' [ Elegir ]')+$orgs,'','class="form-control"')?>
    </div>
    <div>
        <button type="submit" class="btn btn-success">Generar</button>
    </div>
    <?php echo form_close();?>
</section>