<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<!-- IF YOU ARE USING TEXT OUTSIDE OF THE CONTAINER (NOT RECOMENDED), ENCLOSE IT WITH <P> TAGS. -->
    <div class="container theme-showcase" role="main">

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron">
        <h1>DarkRP Lockpick Customiser</h1>
	<p>Customise your lockpicks!</p>
    </div>
<div class="row">
  <div class="col-xs-12 col-md-8">

<p>
<div class="alert alert-info" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> So far, we have generated a total of <?php include('success.txt'); ?> lockpicks!</div>

<?php 
if(!isset($_POST["min"])) {
include('form.php');
} else {
// BEGIN CLUSTERFUCK
$code = 'AddCSLuaFile()
if SERVER then
	util.AddNetworkString("lockpick_time")
end
if CLIENT then
	SWEP.PrintName = "'. ($_POST["name"]) .'"
	SWEP.Slot = 5
	SWEP.SlotPos = 1
	SWEP.DrawAmmo = false
	SWEP.DrawCrosshair = false
end
SWEP.Author = "Lockpick Maker by Connor / DarkRP Developers"
SWEP.Instructions = "Left or right click to pick a lock"
SWEP.Contact = ""
SWEP.Purpose = "'. ($_POST["purpose"]) .'"
SWEP.ViewModelFOV = '. ($_POST["fov"]) .'
SWEP.ViewModelFlip = '. ($_POST["flip"]) .'
SWEP.ViewModel = Model("'. ($_POST["viewmodel"]) .'")
SWEP.WorldModel = Model("'. ($_POST["worldmodel"]) .'")
SWEP.UseHands = true
SWEP.Spawnable = true
SWEP.AdminOnly = true
SWEP.Category = "DarkRP (Utility)"
SWEP.Sound = Sound("physics/wood/wood_box_impact_hard3.wav")
SWEP.Primary.ClipSize = -1      -- Size of a clip
SWEP.Primary.DefaultClip = 0        -- Default number of bullets in a clip
SWEP.Primary.Automatic = false      -- Automatic/Semi Auto
SWEP.Primary.Ammo = ""
SWEP.Secondary.ClipSize = -1        -- Size of a clip
SWEP.Secondary.DefaultClip = -1     -- Default number of bullets in a clip
SWEP.Secondary.Automatic = false        -- Automatic/Semi Auto
SWEP.Secondary.Ammo = ""
SWEP.LockPickTime = 30
--[[-------------------------------------------------------
Name: SWEP:Initialize()
Desc: Called when the weapon is first loaded
---------------------------------------------------------]]
function SWEP:Initialize()
	self:SetHoldType("normal")
end
if CLIENT then
	net.Receive("lockpick_time", function()
		local wep = net.ReadEntity()
		local ent = net.ReadEntity()
		local time = net.ReadUInt(5)

		wep.IsLockPicking = true
		wep.LockPickEnt = ent
		wep.StartPick = CurTime()
		wep.LockPickTime = time
		wep.EndPick = CurTime() + time

		wep.Dots = wep.Dots or ""
		timer.Create("LockPickDots", 0.5, 0, function()
			if not IsValid(wep) then timer.Destroy("LockPickDots") return end
			local len = string.len(wep.Dots)
			local dots = {[0]=".", [1]="..", [2]="...", [3]=""}
			wep.Dots = dots[len]
		end)
	end)
end
--[[-------------------------------------------------------
Name: SWEP:PrimaryAttack()
Desc: +attack1 has been pressed
---------------------------------------------------------]]
function SWEP:PrimaryAttack()
	self.Weapon:SetNextPrimaryFire(CurTime() + 2)
	if self.IsLockPicking then return end

	local trace = self.Owner:GetEyeTrace()
	local ent = trace.Entity

	if not IsValid(ent) then return end
	local canLockpick = hook.Call("canLockpick", nil, self.Owner, ent)

	if canLockpick == false then return end
	if canLockpick ~= true and (
			trace.HitPos:Distance(self.Owner:GetShootPos()) > 100 or
			(not GAMEMODE.Config.canforcedooropen and ent:GetKeysNonOwnable()) or
			(not ent:isDoor() and not ent:IsVehicle() and not string.find(string.lower(ent:GetClass()), "vehicle") and (not GAMEMODE.Config.lockpickfading or not ent.isFadingDoor))
		) then
		return
	end
	self:SetHoldType("pistol")
	if CLIENT then return end
	local onFail = function(ply) if ply == self.Owner then hook.Call("onLockpickCompleted", nil, ply, false, ent) end end
	-- Lockpick fails when dying or disconnecting
	hook.Add("PlayerDeath", self, fc{onFail, fn.Flip(fn.Const)})
	hook.Add("PlayerDisconnected", self, fc{onFail, fn.Flip(fn.Const)})
	-- Remove hooks when finished
	hook.Add("onLockpickCompleted", self, fc{fp{hook.Remove, "PlayerDisconnected", self}, fp{hook.Remove, "PlayerDeath", self}})
	self.IsLockPicking = true
	self.LockPickEnt = ent
	self.StartPick = CurTime()
	self.LockPickTime = math.Rand('. ($_POST["min"]) .', '. ($_POST["max"]) .')
	net.Start("lockpick_time")
		net.WriteEntity(self)
		net.WriteEntity(ent)
		net.WriteUInt(self.LockPickTime, 5) -- 2^5 = 32 max
	net.Send(self.Owner)
	self.EndPick = CurTime() + self.LockPickTime
	timer.Create("LockPickSounds", 1, self.LockPickTime, function()
		if not IsValid(self) then return end
		local snd = {1,3,4}
		self:EmitSound("'. ($_POST["picksound"]) .'".. tostring(snd[math.random(1, #snd)]) ..".wav", 50, 100)
	end)
end
function SWEP:Holster()
	self.IsLockPicking = false
	self.LockPickEnt = nil
	if SERVER then timer.Destroy("LockPickSounds") end
	if CLIENT then timer.Destroy("LockPickDots") end
	return true
end
function SWEP:Succeed()
	self:SetHoldType("normal")

	local ent = self.LockPickEnt
	self.IsLockPicking = false
	self.LockPickEnt = nil

	if SERVER then timer.Destroy("LockPickSounds") end
	if CLIENT then timer.Destroy("LockPickDots") end

	if not IsValid(ent) then return end

	local override = hook.Call("onLockpickCompleted", nil, self.Owner, true, ent)

	if override then return end

	if ent.isFadingDoor and ent.fadeActivate and not ent.fadeActive then
		ent:fadeActivate()
		timer.Simple(5, function() if IsValid(ent) and ent.fadeActive then ent:fadeDeactivate() end end)
	elseif ent.Fire then
		ent:keysUnLock()
		ent:Fire("open", "", .6)
		ent:Fire("setanimation", "open", .6)
	end
end
function SWEP:Fail()
	self.IsLockPicking = false
	self:SetHoldType("normal")

	hook.Call("onLockpickCompleted", nil, self.Owner, false, self.LockPickEnt)
	self.LockPickEnt = nil

	if SERVER then timer.Destroy("LockPickSounds") end
	if CLIENT then timer.Destroy("LockPickDots") end
end
function SWEP:Think()
	if not self.IsLockPicking or not self.EndPick then return end

	local trace = self.Owner:GetEyeTrace()
	if not IsValid(trace.Entity) or trace.Entity ~= self.LockPickEnt or trace.HitPos:Distance(self.Owner:GetShootPos()) > 100 then
		self:Fail()
	elseif self.EndPick <= CurTime() then
		self:Succeed()
	end
end
function SWEP:DrawHUD()
	if not self.IsLockPicking or not self.EndPick then return end

	self.Dots = self.Dots or ""
	local w = ScrW()
	local h = ScrH()
	local x,y,width,height = w/2-w/10, h/2-60, w/5, h/15
	draw.RoundedBox(8, x, y, width, height, Color(10,10,10,120))

	local time = self.EndPick - self.StartPick
	local curtime = CurTime() - self.StartPick
	local status = math.Clamp(curtime/time, 0, 1)
	local BarWidth = status * (width - 16)
	local cornerRadius = math.Min(8, BarWidth/3*2 - BarWidth/3*2%2)
	draw.RoundedBox(cornerRadius, x+8, y+8, BarWidth, height-16, Color(255-(status*255), 0+(status*255), 0, 255))

	draw.DrawNonParsedSimpleText("'. ($_POST["picktext"]) .'" .. self.Dots, "'. ($_POST["pickfont"]) .'", w/2, y + height/2, Color(255,255,255,255), 1, 1)
end
function SWEP:SecondaryAttack()
	self:PrimaryAttack()
end
DarkRP.hookStub{
	name = "canLockpick",
	description = "Whether an entity can be lockpicked.",
	parameters = {
		{
			name = "ply",
			description = "The player attempting to lockpick an entity.",
			type = "Player"
		},
		{
			name = "ent",
			description = "The entity being lockpicked.",
			type = "Entity"
		},
	},
	returns = {
		{
			name = "allowed",
			description = "Whether the entity can be lockpicked",
			type = "boolean"
		}
	},
	realm = "Server"
}
DarkRP.hookStub{
	name = "onLockpickCompleted",
	description = "Result of a player attempting to lockpick an entity.",
	parameters = {
		{
			name = "ply",
			description = "The player attempting to lockpick the entity.",
			type = "Player"
		},
		{
			name = "success",
			description = "Whether the player succeeded in lockpicking the entity.",
			type = "boolean"
		},
		{
			name = "ent",
			description = "The entity that was lockpicked.",
			type = "Entity"
		},
	},
	returns = {
		{
			name = "override",
			description = "Return true to override default behaviour, which is opening the (fading) door.",
			type = "boolean"
		}
	},
	realm = "Shared"
}
';
// END CLUSTERFUCK
# Creating the array
    $data = array(
        'description' => 'DarkRP Lockpick Maker',
        'public' => true,
        'files' => array(
            'shared.lua' => array('content' => $code),
        ),
    );                               
    $data_string = json_encode($data);

    # Sending the data using cURL
    $url = 'https://api.github.com/gists';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "cm-pi.xyz - DarkRP Lockpick Maker");
    $response = curl_exec($ch);
    curl_close($ch);

    # Parsing the response
    $decoded = json_decode($response, TRUE);
    $gistlink = $decoded['html_url'];
    $gisturl = '<a href="'. $gistlink .'">' . $gistlink .'</a>';
    $dirname = preg_replace('/\s+/', '_', $_POST["name"]);
    $dirname = strtolower($dirname);
    $gistid = $string = str_replace('https://gist.github.com/', '', $gistlink);

if ($gistlink == "") {
    echo('<script type="text/javascript">swal("Something went wrong!", "GitHub Gist Error:\n'. $response .'", "error")</script>');
    include('form.php');
} else {
    echo('<script type="text/javascript">swal("Lockpick Created!", "Please visit '. $gistlink .' or use the box below instructions to preview and download your code.\nThank you for using my Lockpick Creator.", "success")</script>');
    echo('<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-wrench" aria-hidden="true"></span> Please visit ' . $gisturl . ' or use the box below to download the code for your lockpick. Place the file into the /lua/weapons/'. $dirname .' directory named as shared.lua. Thank you for using my DarkRP Lockpick Creator! <a href="#again">Click here to make another...</a></div>');
    echo('<script src="https://gist.github.com/anonymous/'. $gistid .'.js"></script>');
    echo('<a id="again"></a><h3>Make another?</h3>');
    include('form.php');
require('../../includes/pusher.php');
$app_id = '105924';
$app_key = '0d1786e40e74703e94f5';
$app_secret = 'eb878a11102b907ddb7a';

$pusher = new Pusher($app_key, $app_secret, $app_id);

$data['message'] = 'Just created a lockpick!';
$data['title'] = 'Someone';
$pusher->trigger('notify', 'alert', $data);

// START PICK COUNT
$counter_name1 = "success.txt";
// Check if a text file exists. If not create one and initialize it to zero.
if (!file_exists($counter_name1)) {
  $f1 = fopen($counter_name1, "w");
  fwrite($f1,"0");
  fclose($f1);
}

// Read the current value of our counter file
$f1 = fopen($counter_name1,"r");
$counterVal1 = fread($f1, filesize($counter_name1));
fclose($f1);

// Has visitor been counted in this session?
// If not, increase counter value by one
  $counterVal1++;
  $f1 = fopen($counter_name1, "w");
  fwrite($f1, $counterVal1);
  fclose($f1); 
// END PICK COUNT

}
}
?>

</p>

</div>
  <div class="col-xs-6 col-md-4">

<ol>
<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Sidebar -->
<ins class="adsbygoogle"
     style="display:inline-block;width:300px;height:600px"
     data-ad-client="ca-pub-2133002962633579"
     data-ad-slot="7849378447"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
</ol>


</div>
</div>    </div> <!-- /container -->

<?php
require('../includes/footer.php');
?>
<!-- DO COUNT -->
<?php
$counter_name = "count.txt";
// Check if a text file exists. If not create one and initialize it to zero.
if (!file_exists($counter_name)) {
  $f = fopen($counter_name, "w");
  fwrite($f,"0");
  fclose($f);
}

// Read the current value of our counter file
$f = fopen($counter_name,"r");
$counterVal = fread($f, filesize($counter_name));
fclose($f);

// Has visitor been counted in this session?
// If not, increase counter value by one
  $counterVal++;
  $f = fopen($counter_name, "w");
  fwrite($f, $counterVal);
  fclose($f); 
?>
  </body>
</html>
