Database used: PostgreSQL

<h3>Table Kurir</h3>
<table>
  <thead>
    <tr>
      <th colspan="2">
        Kurir
      </th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        <b>id</b>
      </td>
      <td>
        int [primary key, auto increment]
      </td>
    </tr>
    <tr>
      <td>
        <b>nama_kurir</b>
      </td>
      <td>
        character varying (255)
      </td>
    </tr>
    <tr>
      <td>
        <b>no_telepon</b>
      </td>
      <td>
        character varying (15)
      </td>
    </tr>
    <tr>
      <td>
        <b>alamat</b>
      </td>
      <td>
        character varying (500)
      </td>
    </tr>
    <tr>
      <td>
        <b>level</b>
      </td>
      <td>
        int [nilai: 1 - 5, default: 1]
      </td>
    </tr>
    <tr>
      <td>
        <b>status</b>
      </td>
      <td>
        character varying (50) [nilai: "active" | "inactive"]
      </td>
    </tr>
    <tr>
      <td>
        <b>created_at</b>
      </td>
      <td>
        timestamp
      </td>
    </tr>
    <tr>
      <td>
        <b>updated_at</b>
      </td>
      <td>
        timestamp
      </td>
    </tr>
  </tbody>
</table>
<br>
<br>
<br>
<h1>Route</h1>
<br>
<h6>GET: /kurir/all</h6> 

Menampilkan semua data kurir

<h6>GET: /kurir</h6>

Menampilkan data kurir dengan pagination, contoh payload:
```json

  search: "fonda+Rasendria",
  level: "1,2",
  order: "level",
  order_mode: "desc"
  size: 10,
  page: 1
 
```

<h6>POST: /kurir</h6>

Menambahkan data kurir ke database, contoh payload:
```json

  nama_kurir: "Kurir Satu", // required|string|max:255
  no_telepon: "01234567890", // required|string|max:15
  alamat: "Jl. Contoh Alamat 1", // required|string|max:500
  level: 1, // required|integer|in:1,2,3,4,5
  status: "active" // required|string|in:"active","inactive"
 
```

<h6>POST: /kurir/{id}</h6>

Mengedit salah satu data kurir dari database, contoh payload:
```json

  nama_kurir: "Kurir Satu", // string|max:255
  no_telepon: "01234567890", // string|max:15
  alamat: "Jl. Contoh Alamat 1", // string|max:500
  level: 1, // integer|in:1,2,3,4,5
  status: "active" // string|in:"active","inactive"
 
```

<h6>DELETE: /kurir/{id}</h6>

Menghapus salah satu data kurir dari database
