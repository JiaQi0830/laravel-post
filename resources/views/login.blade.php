<div class="d-flex flex-row justify-content-center align-items-center">
  <h1> LOGIN </h1>    
</div>

<div class="d-flex flex-row justify-content-center align-items-center">
  <form method="POST" action="{{ route('login') }}">
    @csrf
    <label> Email </label>
      <input type = "email" name = "email"/>
      <br/>
    <label> password </label>
      <input type="password" pattern=".{6,}" name = "password"/>
      <br/>
    <div class="d-flex flex-row justify-content-center align-items-center">
      <button type="submit" value="Submit">Submit</button>
    </div>
  </form>
</div>



<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">