
<h1 style="color: red">Bienvenido</h1> <h2>{{ $myName }}</h2>

<table>
    <tr>
        <td>Title</td>
    </tr>
    <tr>
        <td>Description</td>
    </tr>
</table>

@foreach ($users as $user )
    <p><b>{{ $user->name }}</b></p>
@endforeach

