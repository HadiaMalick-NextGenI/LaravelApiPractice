@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mt-5">Signup Here</h2>
        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
        <form id="create-signup-form" action="#" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" id="password" value="{{ old('password') }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Signup</button>
        </form>
    </div>

    <script>
        document.getElementById('create-signup-form').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('name', document.getElementById('name').value);
            formData.append('email', document.getElementById('email').value);
            formData.append('password', document.getElementById('password').value);

            try{
                const response = await axios.post('http://127.0.0.1:8000/api/v1/signup', formData , {});

                if (response.status === 201) {
                    const token = response.data.token.plainTextToken;

                    document.cookie = `authToken=${token}; path=/; max-age=86400; Secure; SameSite=Strict`;
                    
                    alert('Registration successfull!');
                    window.location.href = '/posts'; 
                }
            } catch(error){
                console.error("Error registering user:", error);
                alert('There was an error creating the user.');
            }
        });
    </script>
@endsection