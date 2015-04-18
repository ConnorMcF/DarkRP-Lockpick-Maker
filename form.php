<form class="form-horizontal" method="POST">
<h4>Lockpick Times</h4>
  <div class="form-group">
    <label for="min" class="col-sm-2 control-label">Minimum Time</label>
    <div class="col-sm-10">
      <input type="number" class="form-control" id="min" name="min" value="10">
      <p class="text-muted">The shortest amount of time it will take to pick the lock.</p>
    </div>
  </div>
  <div class="form-group">
    <label for="max" class="col-sm-2 control-label">Maximum Time</label>
    <div class="col-sm-10">
      <input type="number" class="form-control" id="max" name="max" value="30">
      <p class="text-muted">The longest amount of time it will take to pick the lock.</p>
    </div>
  </div>
<h4>Lockpick Attributes</h4>
<div class="form-group">
    <label for="name" class="col-sm-2 control-label">Name</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="name" name="name" value="Lockpick">
      <p class="text-muted">The name of the lockpick.</p>
    </div>
  </div>
<div class="form-group">
    <label for="picktext" class="col-sm-2 control-label">Picking Text</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="picktext" name="picktext" value="Picking...">
      <p class="text-muted">The text that is shown while lockpicking.</p>
    </div>
  </div>
<div class="form-group">
    <label for="pickfont" class="col-sm-2 control-label">Name</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="pickfont" name="pickfont" value="Trebuchet24">
      <p class="text-muted">The font that is shown while lockpicking.</p>
    </div>
  </div>
<div class="form-group">
    <label for="purpose" class="col-sm-2 control-label">Purpose</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="purpose" name="purpose" value="A lockpick used for breaking locks.">
      <p class="text-muted">The purpose of the lockpick displayed in the info hint.</p>
    </div>
  </div>
<div class="form-group">
    <label for="fov" class="col-sm-2 control-label">FOV</label>
    <div class="col-sm-10">
      <input type="number" class="form-control" id="fov" name="fov" value="62">
      <p class="text-muted">The field of view at which the client sees the model.</p>
    </div>
  </div>
<div class="form-group">
    <label for="flip" class="col-sm-2 control-label">View Model Flip</label>
    <div class="col-sm-10">
      <select class="form-control" name="flip" id="flip">
  <option value="false">No</option>
  <option value="true">Yes</option>
</select>
      <p class="text-muted">Should the client see the view model flipped?</p>
    </div>
  </div>
<h4>Advanced</h4>
<p class="text-danger">Modify with caution!</p>
<div class="form-group">
    <label for="viewmodel" class="col-sm-2 control-label">Viewmodel</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="viewmodel" name="viewmodel" value="models/weapons/c_crowbar.mdl">
      <p class="text-muted">The model of which the client sees.</p>
    </div>
  </div>
<div class="form-group">
    <label for="worldmodel" class="col-sm-2 control-label">Worldmodel</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="worldmodel" name="worldmodel" value="models/weapons/w_crowbar.mdl">
      <p class="text-muted">The model of which is displayed to everyone else.</p>
    </div>
  </div>
<div class="form-group">
    <label for="picksound" class="col-sm-2 control-label">Lockpick Sound</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" id="picksound" name="picksound" value="weapons/357/357_reload">
      <p class="text-muted">The sound that is looped while lockpicking.</p>
    </div>
  </div>

  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <button type="submit" class="btn btn-default">Submit</button>
    </div>
  </div>
</form>