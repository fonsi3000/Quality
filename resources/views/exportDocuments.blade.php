<table>
    <thead>
        <tr>
            <th>Nombre del documento</th>
            <th>Tipo de documento</th>
            <th>versión</th>
            <th>fecha de actualización</th>
            <th>proceso</th>
            <th>estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($report as $re)
            <tr>
                <td>{{$re->document_name}}</td>
                <td>{{$re->documentType->name}}</td>
                <td>{{$re->version}}</td>
                <td>{{$re->updated_at}}</td>
                <td>{{$re->process->name}}</td>
                <td>{{$re->status}}</td>
            </tr>
        @endforeach
    </tbody>
</table>