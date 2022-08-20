
<form action="{{ route('auth_login') }}" method="post" >
    @csrf
    <input name="username" />
    <input name="password" />
    <button type="submit">login</button>
</form>
