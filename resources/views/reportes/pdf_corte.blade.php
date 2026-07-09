<h1>Reporte de Cortes</h1>
<table border="1" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Servicio</th>
            <th>Precio</th>
        </tr>
    </thead>
    <tbody>
        @foreach($historialCortes as $corte)
        <tr>
            <td>{{ $corte->fecha_hora }}</td>
            <td>{{ $corte->cliente->name ?? 'General' }}</td>
            <td>{{ $corte->servicio->nombre }}</td>
            <td>${{ $corte->precio }}</td>
        </tr>
        @endforeach
    </tbody>
</table>