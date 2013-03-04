<h2>Login</h2>
{if $errors|@count > 0}
Das Formular wurde nicht korrekt ausgefüllt
<ul>
{foreach $errors as $error}
  <li>{$error}</li>
{/foreach}
{/if}
</ul>

<p>{$test}</p>

<form class="form-horizontal" action="index.php" method="POST">
  <div class="control-group">
    <input type="hidden" name="action" value="login" />
    <label class="control-label" for="username">Benutzername</label>
    <input class="controls{if $request && !$request.username} wrongInput{/if}" type="text" id="username" name="username"  value="{if $request && $request.username}{$request.username}{/if}" autocomplete="off" />
  </div>
  <div class="control-group">
    <label class="control-label" for="password">Passwort:</label>
    <input class="controls{if $request && !$request.password} wrongInput{/if}" type="password" name="password" value="{if $request && $request.password}{$request.password}{/if}"/>
  </div>

  <div class="control-group">

  <input class="btn btn-primary btn-large" type="submit" value="Login" />
  </div>
</form>


