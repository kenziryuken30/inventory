<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Inventory</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', sans-serif;
}

body{
    height:100vh;
    display:flex;
    background:#f3f4f6;
}


.left{
    width:55%;
    position:relative;
    background:linear-gradient(180deg,#2ec4c7 0%, #168c94 100%);
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}

.logo{
    width:320px; 
    position:relative;
    z-index:2;
}

.tools{
    position:absolute;
    bottom:0;
    left:0;
    width:100%;   
    object-fit:cover;
}


.right{
    width:45%;
    display:flex;
    align-items:center;
    justify-content:center;
}


.login-card{
    width:420px;
    background:#ffffff;
    padding:45px;
    border-radius:18px;
    box-shadow:0 20px 50px rgba(0,0,0,0.07);
}

.login-card h2{
    font-size:24px;
    font-weight:700;
    margin-bottom:5px;
    color:#222;
}

.login-card p{
    font-size:14px;
    color:#7a7a7a;
    margin-bottom:30px;
}

.login-card label{
    font-size:14px;
    font-weight:600;
    color:#444;
}

.login-card input{
    width:100%;
    padding:14px;
    margin-top:8px;
    margin-bottom:22px;
    border-radius:10px;
    border:1px solid #e4e6eb;
    background:#eef2f7;
    font-size:14px;
}

.login-card input:focus{
    outline:none;
    border-color:#4DB6AC;
    background:#ffffff;
}

.login-card button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:10px;
    background:linear-gradient(90deg,#63b8af,#4db6ac);
    color:white;
    font-weight:600;
    font-size:15px;
    cursor:pointer;
    box-shadow:0 8px 20px rgba(77,182,172,0.25);
    transition:0.2s;
}

.login-card button:hover{
    transform:translateY(-2px);
}

.error{
    color:red;
    font-size:13px;
    margin-bottom:15px;
}
</style>
</head>
<body>

<div class="left">
    <img src="{{ asset('images/artimu.png') }}" class="logo" alt="logo">
    <img src="{{ asset('images/WALPAPER TOOLS.png') }}" class="tools" alt="tools">
</div>

<div class="right">
    <div class="login-card">
        <h2>Login</h2>
        <p>System Inventory Management</p>

        @if ($errors->any())
            <div class="error">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>

            <button type="submit">Sign In</button>
        </form>
    </div>
</div>

</body>
</html>